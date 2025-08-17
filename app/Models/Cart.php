<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory;

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
    public function items(): HasMany
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
        if ($this->relationLoaded('items')) {
            $this->items->loadMissing('product');
        } else {
            $this->loadMissing('items.product');
        }

        return $this->items->sum(function ($item) {
            $price = $item->product->base_price ?? 0;
            
            $user = auth('sanctum')->user();
            if ($user && $user->role === 'distributor') {
                $price = $item->product->distributor_price ?? $price;
            }

            return $item->quantity * $price;
        });
    }
}
