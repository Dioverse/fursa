<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Discount extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory, Auditable;
    
    public function users() {
        return $this->belongsToMany(User::class, 'discount_user');
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
