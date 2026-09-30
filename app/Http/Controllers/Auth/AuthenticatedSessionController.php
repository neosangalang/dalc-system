<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Redirect based on role
        return match($user->role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'teacher'  => redirect()->route('teacher.dashboard'),
            'guardian' => redirect()->route('guardian.dashboard'),
            default    => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // NOTE: We intentionally do NOT clear the mfa_remember cookie here.
        // "Remember this device for 30 days" means the DEVICE stays trusted
        // across login/logout cycles, just like Google's "Trust this computer".
        // The cookie expires naturally after 30 days.

        return redirect('/');
    }
}
