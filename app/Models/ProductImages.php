<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ProductImages extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = ['product_id', 'path'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
