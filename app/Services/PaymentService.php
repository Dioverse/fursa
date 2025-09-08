<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    public function verifyAndSave($gateway, $transId, $order, $cartTotal)
    {
        $payment = $gateway->verifyPayment($transId);

        if (!$payment['success']) {
            return ['error' => true, 'message' => 'Payment verification failed'];
        }

        $amount = $payment['amount'] / 100;

        if (bccomp((string)$amount, (string)$cartTotal, 2) !== 0) {
            return ['error' => true, 'message' => 'Invalid transaction amount'];
        }

        $paymentRecord = Payment::updateOrCreate(
            ['transaction_reference' => $payment['reference']],
            [
                'user_id'   => $order->user_id,
                'order_id'  => $order->id,
                'status'    => $payment['status'],
                'paid_at'   => now(),
                'amount'    => $payment['amount'],
                'currency'  => $payment['currency'],
                'method'    => $payment['method'],
                'gateway'   => $payment['gateway'],
                'raw'       => json_encode($payment['raw']),
            ]
        );

        return ['error' => false, 'payment' => $paymentRecord, 'status' => $payment['status']];
    }
}
