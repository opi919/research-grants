<?php

// app/Http/Controllers/Auth/AuthController.php
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'institution' => 'required|string|max:255',
            'purpose' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with(
            'success',
            'Registration successful! Your account is pending admin approval.'
        );
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        if ($user->status !== 'approved') {
            return back()->withErrors(['email' => 'Your account is not approved yet.']);
        }

        $deviceFingerprint = $this->generateDeviceFingerprint($request);

        // Check if user has active session on different device
        if ($user->hasActiveSessionOnDifferentDevice($deviceFingerprint)) {
            return back()->withErrors(['device' =>
            'You can only access from one device. Please logout from other devices first.']);
        }

        // Login the user
        Auth::login($user);

        // Create or update session
        $user->createSession(
            $deviceFingerprint,
            $request->ip(),
            $request->userAgent()
        );

        // Update user login info
        $user->update([
            'device_fingerprint' => $deviceFingerprint,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Deactivate current session
            $deviceFingerprint = $this->generateDeviceFingerprint($request);
            $user->userSessions()
                ->where('device_fingerprint', $deviceFingerprint)
                ->update(['is_active' => false]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
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
