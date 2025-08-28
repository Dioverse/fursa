<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'post_category_id', 'title', 'slug',
        'excerpt', 'body', 'featured_image',
        'published', 'published_at'
    ];

    public function category() {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
