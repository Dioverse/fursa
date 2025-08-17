<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    public $table = 'orders';
    public $fillable = [
        'user_id',
        'shipping_address_id',
        'order_id',
        'total_amount',
        'status'
    ];
    
    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}
