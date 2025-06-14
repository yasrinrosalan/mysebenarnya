<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'contact_info',
        'profile_picture_url',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 🔗 Relationships
    public function adminUser()
    {
        return $this->hasOne(AdminUser::class, 'user_id');
    }

    public function agencyUser()
    {
        return $this->hasOne(AgencyUser::class, 'user_id');
    }

    public function publicUser()
    {
        return $this->hasOne(PublicUser::class, 'user_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    // 🛡️ Role Checkers (based on role column)
    public function isAdminUser(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgencyUser(): bool
    {
        return $this->role === 'agency';
    }

    public function isPublicUser(): bool
    {
        return $this->role === 'public';
    }

    public function isMcmcUser(): bool
    {
        return $this->role === 'mcmc';
    }

    // 🧠 Derived Role (fallback)
    public function getDetectedRoleAttribute(): string
    {
        if ($this->isAdminUser()) {
            return 'admin';
        } elseif ($this->isAgencyUser()) {
            return 'agency';
        } elseif ($this->isPublicUser()) {
            return 'public';
        } elseif ($this->isMcmcUser()) {
            return 'mcmc';
        }
        return 'unknown';
    }
}
