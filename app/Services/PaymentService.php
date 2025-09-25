<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    public function verifyAndSave($gateway, $transRef, $orderId, $userId, $cartTotal)
    {
        $payment = $gateway->verifyPayment($transRef);
        print_r($payment);
        if (!$payment['success']) {
            return ['error' => true, 'message' => 'Payment verification failed'];
        }

        $amount = $payment['amount'];

        if (bccomp((string)$amount, (string)$cartTotal, 2) !== 0) {
            print_r($amount);
            print_r($cartTotal);
            print_r(bccomp((string)$amount, (string)$cartTotal, 2));

            return ['error' => true, 'message' => 'Invalid transaction amount'];
        }

        $paymentRecord = Payment::updateOrCreate(
            ['transaction_reference' => $payment['reference']],
            [
                'user_id'           => $userId,
                'order_id'          => $orderId,
                'status'            => $payment['status'],
                'paid_at'           => now(),
                'amount'            => $payment['amount'],
                // 'currency'          => $payment['currency'],
                'payment_method'    => $payment['method'],
                'payment_gateway'   => $payment['gateway'],
                'raw'               => json_encode($payment['raw']),
            ]
        );

        return ['error' => false, 'status' => $payment['status']];
    }
}
