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
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id'
    ];
    
    public function products() {
        return $this->hasMany(Product::class);
    }

    public function productsbySubcats()
    {
        return $this->hasManyThrough(
            Product::class,   // Final model we want
            Category::class,  // Intermediate model (subcategories)
            'parent_id',      // Foreign key on subcategories table (linking to parent category id)
            'category_id',    // Foreign key on products table
            'id',             // Local key on parent categories
            'id'              // Local key on subcategories
        );
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
