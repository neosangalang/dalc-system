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

            // DEBUG: Log the remember_device checkbox value
            \Illuminate\Support\Facades\Log::info('MFA VERIFY DEBUG', [
                'user_id' => $user->id,
                'remember_device_in_request' => $request->has('remember_device'),
                'remember_device_boolean' => $request->boolean('remember_device'),
                'remember_device_raw' => $request->input('remember_device'),
                'all_input' => $request->all(),
            ]);

            // Set 30-day cookie if user checked "Remember this device"
            if ($request->boolean('remember_device')) {
                \Illuminate\Support\Facades\Cookie::queue('mfa_remember_' . $user->id, true, 43200); // 30 days
                \Illuminate\Support\Facades\Log::info('MFA COOKIE SET for user ' . $user->id);
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