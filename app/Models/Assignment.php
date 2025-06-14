<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $primaryKey = 'assignment_id';

    protected $fillable = [
        'status',
        'assigned_at',
        'last_updated_at',
        'comment',
        'inquiry_id',
        'agency_user_id'
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function agencyUser()
    {
        return $this->belongsTo(AgencyUser::class, 'agency_user_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'assignment_id');
    }
}
