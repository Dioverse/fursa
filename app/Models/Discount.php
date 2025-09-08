<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Discount extends Model implements AuditableContract
{
    use Auditable;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function activeDiscount()
    {
        return $this->hasOne(Discount::class)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
