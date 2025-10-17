<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    //as in gs()
    protected $table = 'general_settings';

    protected $fillable = [
        'site_name', 'tax', 'gateways', 'site_logo', 'email_from', 'email_from_name', 'email_template',
        'sms_template', 'sms_from', 'push_title', 'push_template',
        'mail_config', 'sms_config', 'firebase_config', 'en', 'sn', 'pn'
    ];

    protected $casts = [
        'gateways' => 'array',
        'mail_config' => 'array',
        'sms_config' => 'array',
        'firebase_config' => 'array',
        'en' => 'boolean',
        'sn' => 'boolean',
        'pn' => 'boolean',
    ];

    public static function get($key, $default = null)
    {
        $value = static::where('key', $key)->value('value');

        // If null, return default
        if (is_null($value)) return $default;

        // Convert JSON string to array if needed
        $data = is_array($value) ? $value : json_decode($value, true);

        return $data ?: $default;
    }

    /**
     * Get nested value from JSON using dot notation
     * Example: AdminSettings::getNested('gateways.Paystack.secretKey')
     */
    public static function getNested($key, $default = null)
    {
        $segments = explode('.', $key);
        $value = static::get(array_shift($segments));

        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
