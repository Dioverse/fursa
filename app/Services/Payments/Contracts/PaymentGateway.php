<?php

namespace App\Services\Payments\Contracts;

interface PaymentGateway
{
    public function verifyPayment(string $reference): array;
    public function refundPayment(string $reference, int $amount): array;
}