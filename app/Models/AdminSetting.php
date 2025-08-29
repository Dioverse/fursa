<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AdminSetting extends Model implements AuditableContract
{

    use HasFactory, Auditable;
    
    protected $fillable = ['key', 'value'];

    // Helper: get setting by key
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
    
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
