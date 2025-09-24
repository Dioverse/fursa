<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Product extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, Auditable;

    public $table = 'products';
    protected $appends = ['price'];
    //  protected $with = ['images:id,product_id,path'];
    protected $fillable = [
        'category_id',
        'name',
        'short_description',
        'description',
        'base_price',
        'distributor_price',
        'stock_quantity',
        'low_stock_threshold',
        'status',
        'slug',
        'sku',
        'is_featured',
        'tags'
    ];

    protected $casts = [
        'tags'    => 'array',
    ];
    
    public function category() {
        return $this->belongsTo(Category::class);
    }
    
    public function distributorPrices() {
        return $this->hasMany(DistributorProductPrice::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // public function discounts()
    // {
    //     return $this->hasOne(Discount::class);
    // }

    public function toArray()
    {
        $array = parent::toArray();
        $user = auth('sanctum')->user();

        if ($user && $user->role === 'admin') {
            // Admin sees raw prices, hide computed price
            unset($array['price']);
        } else {
            // Everyone else → add computed price, hide raw fields
            $array['price'] = $this->price;
            unset($array['base_price'], $array['distributor_price']);
        }

        // Append discounted_price if product has active discount
        if ($this->activeDiscount()->exists() && ($user && $user->role !== 'admin')) {
            $array['discounted_price'] = $this->discounted_price;
        }

        return $array;
    }

    public function activeDiscount()
    {
        return $this->hasOne(Discount::class)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function getDiscountedPriceAttribute()
    {
        $discount = $this->activeDiscount()->first();
        $price = $this->base_price;
        $user = auth('sanctum')->user();

        if ($user && $user->isDistributorApprov()) { $price = $this->distributor_price; }

        if (!$discount) { return $price; }

        if ($discount->type === 'percentage') { return round($price * (1 - ($discount->value / 100)), 2); }

        // fixed discount
        return max($price - $discount->value, 0);
    }

    public function getPriceAttribute()
    {
        $user = auth('sanctum')->user();

        // If logged in and distributor is approved → distributor price
        if ($user && method_exists($user, 'isDistributorApprov') && $user->isDistributorApprov()) {
            return $this->distributor_price;
        }

        // Otherwise → base price
        return $this->base_price;
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $user = auth('sanctum')->user();

            // If not logged in or not an admin, only show active products
            if (!$user || $user->role !== 'admin') {
                $builder->where('status', 1);
            }
        });

        static::deleting(function ($product) {
            if ($product->images && $product->images->count()) {
                foreach ($product->images as $image) {
                    if ($image->path && Storage::exists($image->path)) {
                        Storage::delete($image->path);
                    }
                    $image->delete();
                }
            }
        });
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
