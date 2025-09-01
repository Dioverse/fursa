<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Services\Payments\Contracts\PaymentGateway;

class PaystackGateway implements PaymentGateway
{
    protected $secretKey;
    protected $baseUrl = "https://api.paystack.co";

    public function __construct($config)
    {
        $this->secretKey = $config['secret_key'];
    }

    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transaction/verify/{$reference}")
                ->throw()
                ->json();

            return [
                'success'   => ($response['status'] ?? false),
                'status' => match($response['data']['status'] ?? null) {
                    'success'   => 'successful',
                    'failed'    => 'failed',
                    'abandoned' => 'cancelled',
                    default     => 'pending',
                },
                'message'   => $response['message'] ?? 'Unknown status',
                'reference' => $response['data']['reference'] ?? null,
                'amount'    => $response['data']['amount'] ?? null,
                'currency'  => $response['data']['currency'] ?? null,
                'gateway'   => 'paystack',
                'method'    => $response['data']['channel'],
                'customer'  => $response['data']['customer'] ?? [],
                'raw'       => $response,
            ];

        } catch (RequestException $e) {
            // This catches HTTP errors like 404 Not Found, 500 Server Error, etc.
            return ['success' => false];
        } catch (\Exception $e) {
            // This catches any other unexpected errors, such as network issues.
            return ['success' => false];
        }
    }

    public function refundPayment(string $reference, $amount = null): array
    {
        try {
            $payload = ['transaction' => $reference];
            $payload['amount'] = $amount * 100;

            $response = Http::withToken($this->secretKey)
                ->post("{$this->baseUrl}/refund", $payload)
                ->throw()
                ->json();

            return [
                'success'   => ($response['status'] ?? false),
                'message'   => $response['message'] ?? 'Refund request failed',
                'reference' => $response['data']['transaction']['reference'] ?? null,
                'refund_id' => $response['data']['id'] ?? null,
                'status'    => $response['data']['status'] ?? null,
                'amount'    => $response['data']['amount'] ?? null,
                'currency'  => $response['data']['currency'] ?? null,
                'gateway'   => 'paystack',
                'raw'       => $response,
            ];

        } catch (RequestException $e) {
            return ['success' => false,];
        } catch (\Exception $e) {
            return ['success' => false,];
        }
    }
}
