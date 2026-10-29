<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use PragmaRX\Google2FA\Google2FA; 

class AccountController extends Controller
{
    public function index() {
        // Grab all users who are either teachers or guardians
        $users = User::whereIn('role', ['teacher', 'guardian'])->get();

        // Grab all students
        $students = Student::orderBy('last_name')->get();
        
        // Pass them to the view using the exact names '$users' and '$students'
        return view('admin.accounts.index', compact('users', 'students'));
    }

    public function store(Request $request) {
        // SECURITY CHECK: Teachers cannot create other Teachers
        if (Auth::user()->role === 'teacher' && $request->role === 'teacher') {
            abort(403, 'Security Violation: Teachers can only create Guardian accounts.');
        }

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'username'          => 'required|string|max:255|unique:users,username',
            'email'             => 'required|email|max:255|unique:users,email',
            'contact_number'    => 'required|regex:/^09[0-9]{9}$/|unique:users,contact_number',
            'role'              => 'required|in:teacher,guardian',
            'password'          => 'required|string|min:8|confirmed',
            'mfa_enabled'       => 'nullable|boolean',
        ], [
            'contact_number.regex'  => 'The contact number must be a valid 11-digit Philippine mobile number starting with 09 (e.g., 09123456789).',
            'contact_number.unique' => 'This contact number is already registered to another account.',
        ]);

        $mfaEnabled = ($validated['role'] === 'guardian')
            ? $request->boolean('mfa_enabled')
            : true;

        $user = User::create([
            'name'           => $validated['name'],
            'username'       => $validated['username'],
            'email'          => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'password'       => Hash::make($validated['password']),
            'role'           => $validated['role'],
            'is_active'      => true,
            'mfa_enabled'    => $mfaEnabled,
        ]);

        return redirect()->back()
            ->with('success', 'Account created successfully!')
            ->with('active_tab', $validated['role'] === 'guardian' ? 'guardians' : 'teachers');
    }
    
    public function toggleStatus(User $account) {
        // SECURITY CHECK: Teachers cannot suspend other Teachers
        if (Auth::user()->role === 'teacher' && $account->role === 'teacher') {
            abort(403, 'Security Violation: Teachers cannot modify other Teacher accounts.');
        }

        if (Auth::id() === $account->id) {
            return redirect()->back()
                ->withErrors(['Cannot deactivate your own admin account.']);
        }

        $account->update(['is_active' => !$account->is_active]);
        $status = $account->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Account successfully {$status}.")
            ->with('active_tab', $account->role === 'guardian' ? 'guardians' : 'teachers');
    }

    public function toggleMfa(Request $request, User $account) {
        $currentUser = Auth::user();

        // 1. Only admin and teacher can perform this action
        if (!in_array($currentUser->role, ['admin', 'teacher'])) {
            abort(403, 'Security Violation: Only administrators and teachers can toggle sign-in MFA settings.');
        }

        // 2. Only guardian accounts can have MFA toggled (staff always require MFA)
        if ($account->role !== 'guardian') {
            return redirect()->back()
                ->withErrors(['Two-Factor Authentication is mandatory for staff accounts and cannot be disabled.']);
        }

        // 3. For teachers without credential management permissions, ensure they teach at least one child of this guardian
        if ($currentUser->role === 'teacher' && !$currentUser->can_manage_credentials) {
            $isAssignedTeacher = Student::where('guardian_id', $account->id)
                ->where('teacher_id', $currentUser->id)
                ->exists();

            if (!$isAssignedTeacher) {
                abort(403, 'Security Violation: You can only configure MFA for guardians of your assigned students.');
            }
        }

        // 4. Toggle mfa_enabled
        $account->mfa_enabled = !$account->mfa_enabled;

        // If disabling MFA, clear two_factor_secret so if re-enabled later the guardian can scan a fresh QR code
        if (!$account->mfa_enabled) {
            $account->two_factor_secret = null;
        }

        $account->save();

        $statusLabel = $account->mfa_enabled ? 'MFA Required' : 'Normal Sign-In';
        $message = "Authentication mode for {$account->name} updated to {$statusLabel}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mfa_enabled' => $account->mfa_enabled,
                'status_label' => $statusLabel,
                'message' => $message,
            ]);
        }

        return redirect()->back()
            ->with('success', $message)
            ->with('active_tab', 'guardians');
    }

    public function toggleEditPermission(User $account) {
        // SECURITY CHECK: Only Master Admins can grant/revoke Teacher editing permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Security Violation: Only system administrators can modify editing permissions.');
        }

        if ($account->role !== 'teacher') {
            return back()->withErrors(['Only teachers can be granted student editing permissions.']);
        }

        $account->update(['can_edit_students' => !$account->can_edit_students]);
        
        $status = $account->can_edit_students ? 'granted' : 'revoked';
        
        return back()->with('success', "Student editing permissions successfully {$status} for {$account->name}.")
            ->with('active_tab', 'teachers');
    }
    
    public function destroy(User $account) {
        // SECURITY CHECK: Teachers cannot delete other Teachers
        if (Auth::user()->role === 'teacher' && $account->role === 'teacher') {
            abort(403, 'Security Violation: Teachers cannot delete Teacher accounts.');
        }

        if (Auth::id() === $account->id) {
            return redirect()->back()
                ->withErrors(['Cannot delete your own admin account.']);
        }

        $tab = request('tab', $account->role === 'guardian' ? 'guardians' : 'teachers');
        $account->delete();
        
        return redirect()->back()
            ->with('success', "Account permanently deleted.")
            ->with('active_tab', $tab);
    }

    public function updateCredentials(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $admin = Auth::user(); 

        // SECURITY CHECK: Teachers cannot reset passwords for other Teachers
        if ($admin->role === 'teacher' && $user->role === 'teacher') {
            abort(403, 'Security Violation: Teachers cannot modify Teacher login credentials.');
        }

        // 1. VALIDATE INCOMING DATA (INCLUDING MFA CODE & UNIQUE CONSTRAINTS)
        $request->validate([
            'username'       => 'required|string|max:255|unique:users,username,' . $id,
            'email'          => 'required|email|max:255|unique:users,email,' . $id,
            'contact_number' => 'nullable|regex:/^09[0-9]{9}$/|unique:users,contact_number,' . $id,
            'password'       => 'nullable|string|min:8',
            'mfa_code'       => 'required|numeric|digits:6',
        ], [
            'contact_number.regex'  => 'The contact number must be a valid 11-digit Philippine mobile number starting with 09 (e.g., 09123456789).',
            'contact_number.unique' => 'This contact number is already registered to another account.',
        ]);

        // 2. SECURITY CHECK: VERIFY ADMIN'S MFA CODE
        $google2fa = new Google2FA();
        
        if (empty($admin->two_factor_secret)) {
            return back()->withErrors(['mfa_code' => 'Two-Factor Authentication is not configured for your account. Please set it up in your profile first.'])->withInput();
        }

        try {
            $isMfaValid = $google2fa->verifyKey($admin->two_factor_secret, $request->mfa_code);
        } catch (\Exception $e) {
            return back()->withErrors(['mfa_code' => 'There is a problem with your MFA configuration.'])->withInput();
        }

        if (!$isMfaValid) {
            return back()->withErrors(['mfa_code' => 'Security verification failed. Invalid Authenticator Code.'])->withInput();
        }

        // 3. APPLY CHANGES IF MFA PASSES
        $user->username = $request->username;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;

        if ($user->role === 'guardian' && $request->has('mfa_enabled_present')) {
            $user->mfa_enabled = $request->boolean('mfa_enabled');
            if (!$user->mfa_enabled) {
                $user->two_factor_secret = null;
            }
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->password_changed_at = null; 
        }

        $user->save();

        return redirect()->back()
            ->with('success', "Login credentials for {$user->name} have been successfully updated!")
            ->with('active_tab', $user->role === 'guardian' ? 'guardians' : 'teachers');
    }

    public function updatePermissions(Request $request, $id)
    {
        // SECURITY CHECK: Only Master Admins can modify advanced module permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Security Violation: Only system administrators can modify module permissions.');
        }

        $user = User::findOrFail($id);
        
        $user->update([
            'can_manage_credentials'   => $request->has('can_manage_credentials'),
            'can_manage_calendar'      => $request->has('can_manage_calendar'),
            'can_create_profiles'      => $request->has('can_create_profiles'),
            'can_archive_students'     => $request->has('can_archive_students'),
            'can_approve_reports'      => $request->has('can_approve_reports'),
            'can_manage_announcements' => $request->has('can_manage_announcements'),
        ]);

        return redirect()->back()
            ->with('success', "Special permissions updated for {$user->name}.")
            ->with('active_tab', 'teachers');
    }
}