<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    //as in gs()
    protected $table = 'general_settings';

    protected $fillable = [
        'site_name', 'site_logo', 'email_from', 'email_from_name', 'email_template',
        'sms_template', 'sms_from', 'push_title', 'push_template',
        'mail_config', 'sms_config', 'firebase_config', 'en', 'sn', 'pn'
    ];

    protected $casts = [
        'mail_config' => 'array',
        'sms_config' => 'array',
        'firebase_config' => 'array',
        'en' => 'boolean',
        'sn' => 'boolean',
        'pn' => 'boolean',
    ];
}
