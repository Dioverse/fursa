<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Distributor;
use OwenIt\Auditing\Auditable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class User extends Authenticatable implements MustVerifyEmail, AuditableContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'status',
        'ban',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin() { return $this->role === 'admin'; }

    public function isCustomer() { return $this->role === 'customer'; }
    
    public function isDistributor() { return $this->role === 'distributor'; }
    
    public function isApproved() { return $this->status === 'approved'; }
    
    public function isDistributorApprov() { return $this->role === 'distributor' && $this->status === 'approved'; }

    public function isDistributorReject() { return $this->role === 'distributor' && $this->status === 'rejected'; }

    public function distributor()
    {
        return $this->hasOne(Distributor::class);
    }

    public function shippingAddress()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function cartItems()
    {
        return $this->hasManyThrough(CartItem::class, Cart::class);
    }

    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    public function wishlistItems()
    {
        return $this->hasManyThrough(WishlistItem::class, Wishlist::class);
    }
}
