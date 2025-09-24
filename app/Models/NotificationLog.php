<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id', 'notification_type', 'sender', 'sent_from', 'sent_to',
        'subject', 'image', 'message', 'status', 'error_message'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
