<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Order extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, Auditable;

    public $table = 'orders';
    protected $fillable = [
        'user_id',
        'shipping_address',
        'order_id',
        'trans_ref',
        'total_amount',
        'status',
        'shipping_cost',
        'tax',
        'delivery_days',
    ];

    protected $casts = [
        'shipping_address'    => 'array',
    ];
    
    public function orderItem() {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHstry() {
        return $this->hasMany(OrderStatusHistory::class);
    }

    // public function shippingAddress()
    // {
    //     return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    // }
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('pending', function (Builder $builder) {
            $user = auth('sanctum')->user();
            if (!$user || $user->role !== 'admin') {
                $builder->where(function ($query) {
                    $query->whereNotIn('status', ['pending'])
                        ->orWhereHas('payment'); // show pending if it has a payment
                });
            }
        });
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
