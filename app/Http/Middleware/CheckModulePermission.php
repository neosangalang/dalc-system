<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = auth()->user();

        // 1. If they are a Master Admin, let them through immediately
        if ($user->role === 'admin') {
            return $next($request);
        }

        // 2. If they are a Teacher, check if the Admin flipped their specific permission switch to ON
        if ($user->role === 'teacher' && $user->$permission) {
            return $next($request);
        }

        // 3. If neither, block them with a 403 error
        abort(403, 'Unauthorized. You do not have permission to access this module.');
    }
}