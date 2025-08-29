<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Post extends Model implements AuditableContract
{
    use HasFactory, Auditable;

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
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
