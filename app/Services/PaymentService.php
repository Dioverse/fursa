<?php
namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    public function verifyAndSave($gateway, $transRef, $orderId, $userId, $cartTotal)
    {
        // Attempt verification
        $payment = $gateway->verifyPayment($transRef);

        // Default values in case verification fails
        $isSuccess = $payment['success'] ?? false;
        $status    = $isSuccess ? ($payment['status'] ?? 'successful') : 'pending';
        $message   = $isSuccess ? ($payment['message'] ?? 'Payment verified successfully') : 'Gateway is currently busy. Try again';

        // Set amount — fallback to cartTotal if gateway didn’t return valid amount
        $amount = $isSuccess ? ($payment['amount'] ?? $cartTotal) : $cartTotal;

        // Create or update payment record
        $record = Payment::updateOrCreate(
            ['transaction_reference' => $transRef],
            [
                'user_id'         => $userId,
                'order_id'        => $orderId,
                'status'          => $status,
                'paid_at'         => $isSuccess ? now() : null,
                'amount'          => $amount,
                'reason'          => $message,
                'payment_method'  => $isSuccess ? ($payment['method'] ?? null) : null,
                'payment_gateway' => $gateway->getName() ?? 'unknown',
                'raw'             => $isSuccess ? json_encode($payment['raw'] ?? []) : null,
            ]
        );

        // If verification failed, return early (but record is still saved)
        if (! $isSuccess) {
            return [
                'error'      => true,
                'message'    => 'Payment verification failed',
                'reason'     => $message,
                'payment_id' => $record->id,
            ];
        }

        // Validate amount consistency
        if (bccomp((string) $amount, (string) $cartTotal, 2) !== 0) {
            return [
                'error'      => true,
                'message'    => 'Invalid transaction amount',
                'reason'     => 'Invalid transaction amount.',
                'payment_id' => $record->id,
            ];
        }

        // Return success
        return [
            'error'      => false,
            'status'     => $status,
            'reason'     => $message,
            'payment_id' => $record->id,
        ];
    }

}
