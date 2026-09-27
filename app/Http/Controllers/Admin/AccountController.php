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
        // Teachers managing credentials can ONLY view Guardian accounts
        if (Auth::user()->role === 'teacher') {
            $users = User::where('role', 'guardian')->get();
        } else {
            $users = User::whereIn('role', ['teacher', 'guardian'])->get();
        }

        // Grab all students
        $students = Student::orderBy('last_name')->get();
        
        return view('admin.accounts.index', compact('users', 'students'));
    }

    public function store(Request $request) {
        // SECURITY CHECK: Teachers cannot create other Teachers
        if (Auth::user()->role === 'teacher') {
            $request->merge(['role' => 'guardian']);
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
            ->with('success', 'Guardian Account created successfully!');
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
            ->with('success', 'Account deleted successfully.');
    }

    public function updatePermissions(Request $request, $id)
    {
        // SECURITY CHECK: Only Master Admins can modify permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Security Violation: Only system administrators can modify module permissions.');
        }

        $user = User::findOrFail($id);

        $user->update([
            'can_manage_credentials' => $request->has('can_manage_credentials'),
            'can_manage_calendar'    => $request->has('can_manage_calendar'),
            'can_create_profiles'    => $request->has('can_create_profiles'),
            'can_archive_students'   => $request->has('can_archive_students'),
            'can_approve_reports'    => $request->has('can_approve_reports'),
        ]);

        return redirect()->back()->with('success', "Permissions updated for {$user->name}.");
    }

    public function updateCredentials(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // SECURITY CHECK: Teachers cannot modify other Teachers
        if (Auth::user()->role === 'teacher' && $user->role === 'teacher') {
            abort(403, 'Security Violation: Teachers can only update Guardian accounts.');
        }

        // Validate basic fields
        $rules = [
            'email'          => 'required|email|max:255|unique:users,email,' . $id,
            'contact_number' => 'required|regex:/^09[0-9]{9}$/|unique:users,contact_number,' . $id,
            'username'       => 'required|string|max:255|unique:users,username,' . $id,
            'mfa_code'       => 'required|digits:6',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8';
        }

        $request->validate($rules, [
            'contact_number.regex'  => 'The contact number must be a valid 11-digit Philippine mobile number starting with 09 (e.g., 09123456789).',
            'contact_number.unique' => 'This contact number is already registered to another account.',
        ]);

        // Verify Admin/User MFA code
        $admin = Auth::user();
        if ($admin->google2fa_secret) {
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($admin->google2fa_secret, $request->mfa_code);
            if (!$valid) {
                return back()->withInput()->withErrors(['mfa_code' => 'Invalid 6-digit MFA code. Verification failed.']);
            }
        }

        $user->email = $request->email;
        $user->contact_number = $request->contact_number;
        $user->username = $request->username;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->must_change_password = true;
        }

        $user->save();

        return redirect()->back()->with('success', "Account credentials updated successfully for {$user->name}.");
    }
}