<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyUser extends Model
{
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'agency_name',
        'agency_contact',
        'force_password_change',
        'admin_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'admin_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'agency_user_id');
    }
}
