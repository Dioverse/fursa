<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['user_id', 'title', 'click_url', 'is_read'];

    protected $casts = [
        'user_id' => 'integer',
        'is_read' => 'boolean',
    ];
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
