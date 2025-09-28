<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    public function verifyAndSave($gateway, $transRef, $orderId, $userId, $cartTotal)
    {
        $payment = $gateway->verifyPayment($transRef);
        if (!$payment['success']) {
            return ['error' => true, 'message' => 'Payment verification failed', 'reason' => 'Gateway is currently busy. Try again'];
        }

        $amount = $payment['amount'];

        if (bccomp((string)$amount, (string)$cartTotal, 2) !== 0) {
            return ['error' => true, 'message' => 'Invalid transaction amount', 'reason' => 'Invalid transaction amount.'];
        }

        Payment::updateOrCreate(
            ['transaction_reference' => $payment['reference']],
            [
                'user_id'           => $userId,
                'order_id'          => $orderId,
                'status'            => $payment['status'],
                'paid_at'           => now(),
                'amount'            => $payment['amount'],
                'reason'            => $payment['message'],
                // 'currency'          => $payment['currency'],
                'payment_method'    => $payment['method'],
                'payment_gateway'   => $payment['gateway'],
                'raw'               => json_encode($payment['raw']),
            ]
        );

        return ['error' => false, 'status' => $payment['status'], 'reason' => $payment['message']];
    }
}
