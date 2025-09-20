<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SingleDeviceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->isAdmin()) {
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $deviceFingerprint = $this->generateDeviceFingerprint($request);

            // Check if current device matches stored device
            if ($user->device_fingerprint && $user->device_fingerprint !== $deviceFingerprint) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'device' => 'You can only access from one device. Please login again.'
                ]);
            }

            // Update session activity
            $session = $user->userSessions()
                ->where('device_fingerprint', $deviceFingerprint)
                ->where('is_active', true)
                ->first();

            if ($session) {
                $session->updateActivity();
            }
        }

        return $next($request);
    }

    private function generateDeviceFingerprint(Request $request)
    {
        $components = [
            $request->userAgent(),
            $request->ip(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];

        return hash('sha256', implode('|', $components));
    }
}
