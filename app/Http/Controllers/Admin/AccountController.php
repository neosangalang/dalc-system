<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use PragmaRX\Google2FA\Google2FA; 

class AccountController extends Controller
{
    public function index() {
        // Grab all users who are either teachers or guardians
        $users = User::whereIn('role', ['teacher', 'guardian'])->get();
        
        // Pass them to the view using the exact name '$users'
        return view('admin.accounts.index', compact('users'));
    }

    public function store(Request $request) {
        // SECURITY CHECK: Teachers cannot create other Teachers
        if (Auth::user()->role === 'teacher' && $request->role === 'teacher') {
            abort(403, 'Security Violation: Teachers can only create Guardian accounts.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|unique:users',
            'email'    => 'required|email|unique:users',
            'role'     => 'required|in:teacher,guardian',
            'password' => 'required|string|min:8|confirmed', // Added 'confirmed' so the two password boxes must match!
        ]);

        User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'is_active'=> true,
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

    public function updateCredentials(\Illuminate\Http\Request $request, $id)
    {
        $user = User::findOrFail($id);
        $admin = Auth::user(); // The person performing the action

        // SECURITY CHECK: Teachers cannot reset passwords for other Teachers
        if ($admin->role === 'teacher' && $user->role === 'teacher') {
            abort(403, 'Security Violation: Teachers cannot modify Teacher login credentials.');
        }

        // 1. VALIDATE INCOMING DATA (INCLUDING MFA CODE)
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email'    => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'mfa_code' => 'required|numeric|digits:6', // Strict 6-digit requirement
        ]);

        // 2. SECURITY CHECK: VERIFY ADMIN'S MFA CODE
        $google2fa = new Google2FA();
        
        // Note: Check your database to ensure your MFA column is named 'google2fa_secret'
        // If you are using Laravel Fortify, it might be named 'two_factor_secret'. Update if necessary!
        $isMfaValid = $google2fa->verifyKey($admin->google2fa_secret, $request->mfa_code);

        if (!$isMfaValid) {
            // Kick them back to the modal with the error and keep what they typed
            return back()->withErrors(['mfa_code' => 'Security verification failed. Invalid Authenticator Code.'])->withInput();
        }

        // 3. APPLY CHANGES IF MFA PASSES
        $user->username = $request->username;
        $user->email = $request->email;

        // Hash the new password securely and attach it to the user if they typed one
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            
            // --- THE MAGIC LINE ---
            // This tells your middleware to trigger the "Force Password Change" screen
            // the very next time this user tries to log in.
            $user->password_changed_at = null; 
        }

        $user->save();

        return redirect()->back()->with('success', "Login credentials for {$user->name} have been successfully updated!");
    }

    public function updatePermissions(\Illuminate\Http\Request $request, $id)
    {
        // SECURITY CHECK: Only Master Admins can modify advanced module permissions
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

        return redirect()->back()->with('success', "Special permissions updated for {$user->name}.");
    }
}