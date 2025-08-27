<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class DistributorProductPrice extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\DistributorProductPriceFactory> */
    use HasFactory, Auditable;

    public $table = 'distributor_product_prices';
    protected $fillable = [
        'product_id',
        'price'
    ];
    
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
