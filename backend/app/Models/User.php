<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Distributor;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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

    public function distributor()
    {
        return $this->hasOne(Distributor::class);
    }

    public function shippingAddress()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }
}
