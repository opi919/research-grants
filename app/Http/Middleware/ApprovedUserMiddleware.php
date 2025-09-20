<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApprovedUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->isAdmin()) {
            return $next($request);
        }

        if (!auth()->check() || !auth()->user()->canAccessDatabase()) {
            return redirect()->route('pending')->with(
                'error',
                'Your account is not approved yet. Please wait for admin approval.'
            );
        }

        $user = auth()->user();
        if ($user->status === 'temporary') {
            // If temporary, check if 1hour has passed since creation then change status to 'pending'
            $created = $user->created_at;
            if ($created->diffInHours(now()) >= 1) {
                $user->status = 'pending';
                $user->save();

                // Log out the user
                auth()->logout();
                return redirect()->route('login')->with(
                    'error',
                    'Your temporary access has expired. Please pay the one time fee to gain full access.'
                );
            }
        }

        return $next($request);
    }
}
