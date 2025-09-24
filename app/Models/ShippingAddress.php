<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ShippingAddress extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\ShippingAddressFactory> */
    use HasFactory, Auditable;

    protected $table = "shipping_addresses";
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address_line_one',
        'address_line_two',
        'province',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
