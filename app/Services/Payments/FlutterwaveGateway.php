<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Client\RequestException;
use App\Services\Payments\Contracts\PaymentGateway;

class FlutterwaveGateway implements PaymentGateway
{
    protected $secretKey;
    protected $baseUrl = "https://api.flutterwave.com/v3";

    public function __construct($config)
    {
        $this->secretKey = $config['secret_key'];
    }

    public function getName(): string
    {
        return 'flutterwave';
    }

    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transactions/{$reference}/verify")
                ->json();

            $resStat = ($response['status'] === 'success' || $response['status'] === true);
            return [
                'success'     => $resStat,
                'status'      => match($response['data']['status'] ?? null) {
                    'successful' => 'successful',
                    'failed'     => 'failed',
                    'pending'    => 'pending',
                    'cancelled'  => 'cancelled',
                    'reversed'   => 'refunded',
                    default      => 'pending',
                },
                'message'     => $response['message'] ?? 'Unknown error',
                'reference'   => $response['data']['tx_ref'] ?? null,
                'amount'      => $response['data']['amount'] ?? null,
                'currency'    => $response['data']['currency'] ?? null,
                'gateway'     => 'flutterwave',
                'method'      => $response['data']['payment_type'] ?? null,
                // 'customer'    => $response['data']['customer'] ?? [],
                'raw'         => $response,
            ];
        // } catch (RequestException $e) {
        //     // This catches HTTP errors like 404 Not Found, 500 Server Error, etc.
        //     return ['success' => false,"kkm"=>$e->getMessage()];
        } catch (\Exception $e) {
            // This catches any other unexpected errors, such as network issues.
            return ['success' => false];
        }
    }

    public function refundPayment(string $transactionId, $amount = null): array
    {
        try {
            $payload = [];
            $payload['amount'] = $amount * 100;

            $response = Http::withToken($this->secretKey)
                ->post("{$this->baseUrl}/transactions/{$transactionId}/refund", $payload)
                ->throw()
                ->json();

            return [
                'success'   => $response['status'] === 'success',
                'message'   => $response['message'] ?? 'Refund request failed',
                'transaction_id' => $transactionId,
                'refund_id' => $response['data']['id'] ?? null,
                'status'    => $response['data']['status'] ?? null,
                'amount'    => $response['data']['amount'] ?? null,
                'currency'  => $response['data']['currency'] ?? null,
                'gateway'   => 'flutterwave',
                'raw'       => $response,
            ];

        } catch (RequestException $e) {
            return ['success' => false,];
        } catch (\Exception $e) {
            return ['success' => false,];
        }
    }
}
