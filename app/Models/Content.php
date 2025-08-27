<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Content extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\ContentFactory> */
    use HasFactory, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'type',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array', // Casts the 'data' column to a PHP array/object automatically
    ];

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
