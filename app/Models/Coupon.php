<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Coupon extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory, Auditable;
    

    public function isValid()
    {
        return $this->start_date <= now()
            && $this->end_date >= now()
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'coupon_user');
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
