<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApprovedUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->canAccessDatabase()) {
            return redirect()->route('pending')->with(
                'error',
                'Your account is not approved yet. Please wait for admin approval.'
            );
        }

        return $next($request);
    }
}
