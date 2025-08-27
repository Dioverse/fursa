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

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
