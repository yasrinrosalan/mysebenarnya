<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = ['user_id', 'department',  'force_password_change'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agencyUsers()
    {
        return $this->hasMany(AgencyUser::class, 'admin_id');
    }
}
