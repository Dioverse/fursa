<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    
    public function category() {
        return $this->belongsTo(Category::class);
    }
    
    public function distributorPrices() {
        return $this->hasMany(DistributorProductPrice::class);
    }
}
