<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OrderItem extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory, Auditable;
    
    protected $table = "order_items";
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
