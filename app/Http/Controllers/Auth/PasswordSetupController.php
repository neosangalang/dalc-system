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
            // Added ->symbols() to enforce the special character requirement matching the frontend
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'agree_terms' => ['required', 'accepted'],
        ], [
            'agree_terms.accepted' => 'You must read and agree to the DALC Terms and Conditions and Privacy Policy to proceed.',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        
        $updateData = [
            'password' => Hash::make($request->password),
            'password_changed_at' => now(), // This unlocks their account!
        ];

        // If terms_accepted_at column is present in the database, record the timestamp
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'terms_accepted_at')) {
            $updateData['terms_accepted_at'] = now();
        }

        $user->update($updateData);

        // For guardian accounts with normal sign-in (MFA optional/disabled), skip MFA setup
        if ($user->role === 'guardian' && !$user->mfa_enabled) {
            return redirect()->route('guardian.dashboard')->with('success', 'Password successfully updated! Welcome to the DALC portal.');
        }

        // If the user already has 2FA configured in their Authenticator app, ask for their 6-digit challenge code
        if (!empty($user->two_factor_secret)) {
            return redirect()->route('security.mfa.challenge')->with('success', 'Password updated! Please enter your 6-digit authenticator code.');
        }

        // Brand new account without 2FA: show the QR code setup for Microsoft/Google Authenticator
        return redirect()->route('security.mfa.setup')->with('success', 'Password updated! Now, please scan the QR code to connect your Authenticator app.');
    }
}