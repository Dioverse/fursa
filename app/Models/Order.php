<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Order extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, Auditable;

    public $table = 'orders';
    public $fillable = [
        'user_id',
        'shipping_address_id',
        'order_id',
        'total_amount',
        'status'
    ];
    
    public function orderItem() {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
