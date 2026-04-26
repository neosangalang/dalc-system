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
            // Check if they clicked the new "Remember this device" box
            if ($request->has('remember_device')) {
                // Give them a VIP Cookie that lasts 30 days (43,200 minutes)
                \Illuminate\Support\Facades\Cookie::queue('mfa_remember_' . $user->id, true, 43200);
            } else {
                // Otherwise, just use the normal temporary session
                session(['mfa_verified' => true]); 
            }
            
            return redirect('/')->with('success', 'Authentication successful.');
        }

        return back()->withErrors(['one_time_password' => 'Invalid authentication code. Please try again.']);
    }
}