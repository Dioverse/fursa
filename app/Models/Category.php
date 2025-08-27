<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Category extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, Auditable;

    public $table = "categories";
    public $fillable = [
        'name',
        'slug',
        'description'
    ];
    
    public function products() {
        return $this->hasMany(Product::class);
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
