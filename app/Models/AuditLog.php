<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $primaryKey = 'log_id';

    protected $fillable = ['action', 'timestamp', 'details', 'inquiry_id', 'user_id'];

    public $timestamps = false; // Disable default timestamps since you're using a custom 'timestamp' column

    // Relationship to inquiries
    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    // Relationship to users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Static method to quickly log an audit entry
     *
     * @param string $action
     * @param string|null $details
     * @param int|null $inquiry_id
     */
    public static function log($action, $details = null, $inquiry_id = null)
    {
        self::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'details'    => $details,
            'timestamp'  => now(),
            'inquiry_id' => $inquiry_id,
        ]);
    }
}
