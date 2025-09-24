<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Wishlist extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\WishlistFactory> */
    use HasFactory, Auditable;

    protected $fillable = [
        'user_id',
    ];

    /**
     * Get the user that owns the wishlist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wishlist items for the wishlist.
     */
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }
    
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}