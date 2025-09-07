<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Product extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, Auditable;

    public $table = 'products';
     protected $with = ['images:id,product_id,path'];
    protected $fillable = [
        'category_id',
        'name',
        'short_description',
        'description',
        'base_price',
        'distributor_price',
        'stock_quantity',
        'low_stock_threshold',
        'status',
        'slug',
        'is_featured',
        'tags'
    ];

    protected $casts = [
        'tags'    => 'array',
    ];
    
    public function category() {
        return $this->belongsTo(Category::class);
    }
    
    public function distributorPrices() {
        return $this->hasMany(DistributorProductPrice::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
