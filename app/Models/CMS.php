<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CMS extends Model implements AuditableContract
{
    use Auditable;

    protected $table = 'cms';

    protected $fillable = [
        'name',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];
    
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
