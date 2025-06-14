<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicUser extends Model
{
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = ['user_id', 'registered_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'public_user_id');
    }
}
