<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class GeneralSetting extends Model implements AuditableContract
{

    use Auditable;
    
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
        $settings = static::first();

        if (!$settings || !array_key_exists($key, $settings->getAttributes())) {
            return $default;
        }

        $value = $settings->$key;

        // Ensure JSON strings are decoded automatically if not cast
        if (is_string($value) && $decoded = json_decode($value, true)) {
            return $decoded;
        }

        return $value ?? $default;
    }

    /**
     * Get nested value from JSON column using dot notation.
     * Example: GeneralSetting::getNested('gateways.paystack.secret_key')
     */
    public static function getNested($key, $default = null)
    {
        $segments = explode('.', $key);
        $column = array_shift($segments);

        $value = static::get($column);

        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
