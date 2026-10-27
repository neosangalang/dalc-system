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
        ], [
            'contact_number.regex'  => 'The contact number must be a valid 11-digit Philippine mobile number starting with 09 (e.g., 09123456789).',
            'contact_number.unique' => 'This contact number is already registered to another account.',
        ]);

        $user = User::create([
            'name'           => $validated['name'],
            'username'       => $validated['username'],
            'email'          => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'password'       => Hash::make($validated['password']),
            'role'           => $validated['role'],
            'is_active'      => true,
        ]);

        return redirect()->back()
            ->with('success', 'Account created successfully!');
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
            ->with('success', "Account successfully {$status}.");
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
        
        return back()->with('success', "Student editing permissions successfully {$status} for {$account->name}.");
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

        $account->delete();
        
        return redirect()->back()
            ->with('success', "Account permanently deleted.");
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

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->password_changed_at = null; 
        }

        $user->save();

        return redirect()->back()->with('success', "Login credentials for {$user->name} have been successfully updated!");
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

        return redirect()->back()->with('success', "Special permissions updated for {$user->name}.");
    }
}