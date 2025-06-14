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
        'is_verified',
        'profile_picture_url',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    // Relationships
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

    // Role helper methods
    public function isAdminUser()
    {
        return $this->adminUser()->exists();
    }

    public function isAgencyUser()
    {
        return $this->agencyUser()->exists();
    }

    public function isPublicUser()
    {
        return $this->publicUser()->exists();
    }

    // Optional: role string accessor (returns 'admin', 'agency', or 'public')
    public function getRoleAttribute()
    {
        if ($this->isAdminUser()) {
            return 'admin';
        } elseif ($this->isAgencyUser()) {
            return 'agency';
        } elseif ($this->isPublicUser()) {
            return 'public';
        }
        return null;
    }
}
