<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'device_fingerprint',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function canAccessDatabase()
    {
        return $this->isApproved() && ($this->isAdmin() || $this->status === 'approved');
    }

    public function userSessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function getActiveSession()
    {
        return $this->userSessions()->where('is_active', true)->first();
    }

    public function hasActiveSessionOnDifferentDevice($deviceFingerprint)
    {
        return $this->userSessions()
            ->where('is_active', true)
            ->where('device_fingerprint', '!=', $deviceFingerprint)
            ->exists();
    }

    public function deactivateAllSessions()
    {
        $this->userSessions()->update(['is_active' => false]);
    }

    public function createSession($deviceFingerprint, $ipAddress, $userAgent)
    {
        // Deactivate all existing sessions for single device policy
        $this->deactivateAllSessions();

        return $this->userSessions()->create([
            'device_fingerprint' => $deviceFingerprint,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'last_activity' => now(),
            'is_active' => true,
        ]);
    }
}