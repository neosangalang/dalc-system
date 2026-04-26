<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordSetupController extends Controller
{
    public function index() {
        return view('auth.security-setup');
    }

    public function store(Request $request) {
        $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'password_changed_at' => now(), // This unlocks their account!
        ]);

        // 🚦 THE TRAFFIC COP: Check the user's role!
        if ($user->role === 'guardian') {
            // Guardians skip MFA and go straight to their dashboard
            return redirect()->route('guardian.dashboard')->with('success', 'Password updated successfully! Welcome to your dashboard.');
        }

        // Admins and Teachers still go to the MFA Setup
        return redirect()->route('security.mfa.setup')->with('success', 'Password updated! Now, please secure your account with Two-Factor Authentication.');
    }
}