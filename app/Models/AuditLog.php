<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $primaryKey = 'log_id';

    protected $fillable = ['action', 'timestamp', 'details', 'inquiry_id', 'user_id'];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
