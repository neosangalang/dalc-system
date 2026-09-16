<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMfaIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // 🚨 DOOR 1: New account (No 2FA secret set up yet) -> Show QR Code setup
            if (is_null($user->two_factor_secret) || trim($user->two_factor_secret) === '') {
                
                if (!$request->routeIs('security.mfa.setup') && !$request->routeIs('security.mfa.verify') && !$request->routeIs('logout')) {
                    return redirect()->route('security.mfa.setup')
                        ->with('warning', 'You must secure your account with Two-Factor Authentication.');
                }
            } 
            // 🚨 DOOR 2: Existing account (Already has 2FA configured) -> Only ask for 6-digit challenge code
            else {
                // Check for BOTH the temporary session AND the 30-day cookie
                $hasSession = session('mfa_verified');
                $hasCookie = $request->cookie('mfa_remember_' . $user->id);

                if ($hasSession || $hasCookie) {
                    // If the cookie remembers this device but the session expired (browser restarted),
                    // restore the session so downstream code can rely on it
                    if (!$hasSession && $hasCookie) {
                        session(['mfa_verified' => true]);
                    }
                } else {
                    if (!$request->routeIs('security.mfa.challenge') && !$request->routeIs('security.mfa.verify-login') && !$request->routeIs('logout')) {
                        return redirect()->route('security.mfa.challenge');
                    }
                }
            }
        }

        return $next($request);
    }
}