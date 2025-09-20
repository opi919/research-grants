<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                => 'required|string|max:255',
            'email'               => 'required|string|email|max:255|unique:users',
            'password'            => 'required|string|min:8|confirmed',
            'institution'         => 'nullable|string|max:255',
            'phone'               => 'required|string|max:13',
            'device_fingerprint'  => 'required|string|min:16|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'password'            => Hash::make($request->password),
            'status'              => 'temporary',
            'phone'               => $request->phone,
            'institution'         => $request->institution,
            'device_fingerprint'  => $request->device_fingerprint,
        ]);

        return redirect()->route('login')->with(
            'success',
            'Registration successful! Your account is temporarily active and locked to this device.'
        );
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        if ($user->status !== 'approved' && $user->status !== 'temporary') {
            return back()->withErrors(['email' => 'Your 1-hour free access has expired. Please pay the one time fee to gain full access.']);
        }

        // 👉 Prefer client fingerprint (stable across IP changes), fallback to server-side hash if missing.
        $currentFingerprint = $request->input('device_fingerprint') ?: $this->serverSideDeviceHash($request);
        if (!$user->device_fingerprint) {
            // For legacy users without a fingerprint yet, you could bind now:
            $user->device_fingerprint = $currentFingerprint;
            $user->save();
        }

        // 🚫 Enforce single-device: must match the one captured at registration
        if (!empty($user->device_fingerprint) && hash_equals($user->device_fingerprint, $currentFingerprint) === false) {
            return back()->withErrors([
                'device' => 'This account is locked to a different device. Please use the device you registered with or contact support.'
            ]);
        }

        // If (for legacy users) no fingerprint was saved yet, you could bind now:
        // if (empty($user->device_fingerprint)) { $user->device_fingerprint = $currentFingerprint; $user->save(); }

        Auth::login($user);

        // Optional: if you still keep a sessions table, mark only this device active
        if (method_exists($user, 'createSession')) {
            $user->createSession($currentFingerprint, $request->ip(), $request->userAgent());
        }

        // Keep last-login metadata (do NOT overwrite device_fingerprint here anymore)
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Deactivate current session if you track sessions
            if (method_exists($user, 'userSessions')) {
                $currentFingerprint = $request->input('device_fingerprint') ?: $this->serverSideDeviceHash($request);
                $user->userSessions()
                    ->where('device_fingerprint', $currentFingerprint)
                    ->update(['is_active' => false]);
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Fallback server-side hash (less stable than client fingerprint,
     * but used only if client value is missing).
     */
    private function serverSideDeviceHash(Request $request): string
    {
        $components = [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];

        return hash('sha256', implode('|', $components));
    }
}
