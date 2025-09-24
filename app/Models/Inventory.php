<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Inventory extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'product_id',
        'user_id',
        'operation',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
