<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Services\Payments\Contracts\PaymentGateway;

class PayOnDeliveryGateway implements PaymentGateway
{
    public function getName(): string
    {
        return 'pay_on_delivery';
    }

    public function verifyPayment($transRef): array
    {
        // POD doesn't require external verification
        // Payment is collected at delivery
        return [
            'success' => false,
            'status' => 'pending',
            'message' => 'Payment will be collected on delivery',
            'method' => 'pay_on_delivery',
            'raw' => ['transaction_ref' => $transRef, 'verified_at' => now()->toDateTimeString()],
        ];
    }
    
    public function refundPayment(string $reference, int $amount): array
    {
        return [];
    }
}