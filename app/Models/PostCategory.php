<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PostCategory extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = ['name', 'slug'];

    public function posts() {
        return $this->hasMany(Post::class);
    }
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}

