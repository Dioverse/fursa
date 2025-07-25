<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    public $table = "categories";
    public $fillable = [
        'name',
        'slug',
        'description'
    ];
    
    public function products() {
        return $this->hasMany(Product::class);
    }
}
