<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DistributorProductPrice extends Model
{
    /** @use HasFactory<\Database\Factories\DistributorProductPriceFactory> */
    use HasFactory;

    public $table = 'distributor_product_prices';
    public $fillable = [
        'product_id',
        'price'
    ];
    
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
