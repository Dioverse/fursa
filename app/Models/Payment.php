<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Payment extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory, Auditable;

    protected $table = 'payments';
    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'reason',
        'payment_gateway',
        'payment_method',
        'transaction_reference',
        'amount',
        'paid_at',
        'refund_status',
        'refunded_at',
        'raw',
        'refund_raw'
    ];

    protected $casts = [
        'paid_at'       => 'datetime',
        'refunded_at'   => 'datetime',
        'raw'           => 'array',
        'refund_raw'    => 'array',
    ];
    
    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isAuditable()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
