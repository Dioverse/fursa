<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public $table = 'products';
    public $fillable = [
        'category_id',
        'name',
        'short_description',
        'description',
        'base_price',
        'distributor_price',
        'category',
        'image',
        'stock_quantity',
        'low_stock_threshold',
        'tags'
    ];
    
    public function category() {
        return $this->belongsTo(Category::class);
    }
    
    public function distributorPrices() {
        return $this->hasMany(DistributorProductPrice::class);
    }
}
