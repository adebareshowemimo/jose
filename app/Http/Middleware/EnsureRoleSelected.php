<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRoleSelected
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user || $user->role_id) {
            return $next($request);
        }

        $allowedRoutes = [
            'auth.complete-signup',
            'auth.complete-signup.submit',
            'auth.logout',
        ];

        if (in_array($request->route()?->getName(), $allowedRoutes, true)) {
            return $next($request);
        }

        return redirect()->route('auth.complete-signup');
    }
}
