<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $primaryKey = 'inquiry_id';

    protected $fillable = [
        'title',
        'description',
        'submitted_at',
        'status',
        'review_notes',
        'is_public',
        'public_user_id',
        'category_id'
    ];

    public function publicUser()
    {
        return $this->belongsTo(PublicUser::class, 'public_user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function assignment()
    {
        return $this->hasOne(Assignment::class, 'inquiry_id');
    }

    


    public function reports()
    {
        return $this->hasMany(Report::class, 'inquiry_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'inquiry_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'inquiry_id');
    }
}
