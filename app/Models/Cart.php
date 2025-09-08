<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Cart extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory, Auditable;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items for the cart.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate the total amount of the cart.
     *
     * @return float
     */
    public function getTotalAmount(): float
    {
        if ($this->relationLoaded('cartItems')) {
            $this->cartItems->loadMissing('product');
        } else {
            $this->loadMissing('cartItems.product');
        }

        return round($this->cartItems->sum(function ($item) {
            $price = $item->product->price ?? 0;

            return $item->quantity * $price;
        }), 2);

        // return round($this->cartItems->sum(function ($item) {
        //     $price = $item->product->base_price ?? 0;
            
        //     $user = auth('sanctum')->user();
        //     if ($user && $user->isDistributorApprov()) {
        //         $price = $item->product->distributor_price ?? $price;
        //     }

        //     return $item->quantity * $price;
        // }), 2);
    }
    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}