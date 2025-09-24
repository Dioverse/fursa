<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Shipping extends Model implements AuditableContract
{

    use SoftDeletes, Auditable;

    protected $fillable = [
        'country',
        'state',
        'province',
        'min_days',
        'max_days',
        'cost',
        'provider',
        'is_active'
    ];

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $user = auth('sanctum')->user();

            // If not logged in or not an admin, only show active shipping rules
            if (!$user || $user->role !== 'admin') {
                $builder->where('is_active', 1);
            }
        });
    }
    
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
