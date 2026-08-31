<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class MfaChallengeController extends Controller
{
    public function index()
    {
        return view('auth.mfa-challenge');
    }

   public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $user = $request->user();

        $isValid = $google2fa->verifyKey($user->two_factor_secret, $request->one_time_password);

        if ($isValid) {
            session(['mfa_verified' => true]);

            // Check if they clicked "Remember this device" or "Remember me" on login
            if ($request->has('remember_device') || session('remember_login')) {
                \Illuminate\Support\Facades\Cookie::queue('mfa_remember_' . $user->id, true, 43200); // 30 days
            }

            return match($user->role) {
                'admin'    => redirect()->route('admin.dashboard')->with('success', 'Authentication successful.'),
                'teacher'  => redirect()->route('teacher.dashboard')->with('success', 'Authentication successful.'),
                'guardian' => redirect()->route('guardian.dashboard')->with('success', 'Authentication successful.'),
                default    => redirect('/')->with('success', 'Authentication successful.'),
            };
        }

        return back()->withErrors(['one_time_password' => 'Invalid authentication code. Please try again.']);
    }
}