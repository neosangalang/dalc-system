<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   public function handle(Request $request, Closure $next): Response
    {
        // STRICT FIX: Using is_null() is safer for database date columns
        if (Auth::check() && is_null(Auth::user()->password_changed_at)) {
            
            if (!$request->routeIs('security.setup') && !$request->routeIs('security.setup.store') && !$request->routeIs('logout')) {
                return redirect()->route('security.setup')
                    ->with('warning', 'For your security, you must set a private password before accessing the DALC system.');
            }
        }

        return $next($request);
    }
}