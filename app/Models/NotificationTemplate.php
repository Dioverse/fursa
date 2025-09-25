<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'act', 'name', 'subject', 'push_title', 'email_body', 'sms_body',
        'push_body', 'shortcodes', 'loop_items', 'email_status', 'email_sent_from_name',
        'email_sent_from_address', 'sms_status', 'sms_sent_from',
        'push_status', 'push_notification_body', 'push_notification_status', 'has_pn'
    ];

    protected $casts = [
        'shortcodes' => 'array',
        'loop_items' => 'array',
        'email_status' => 'boolean',
        'sms_status' => 'boolean',
        'push_status' => 'boolean',
        'push_notification_status' => 'integer',
        'has_pn' => 'boolean',
    ];

}
