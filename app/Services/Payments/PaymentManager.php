<?php

namespace App\Services\Payments;

use App\Models\GeneralSetting;
use Illuminate\Http\JsonResponse;
use App\Services\Payments\Contracts\PaymentGateway;

class PaymentManager
{
    protected $gateways;

    public function __construct()
    {
        $this->gateways = GeneralSetting::get('gateways');
    }

    public function gateway(string $name)
    {

        // Check if the gateway exists and is active
        if (!isset($this->gateways[$name]) || $this->gateways[$name]['status'] !== 'active') {
            return ['error' => true, "message" => ucfirst($name) . " is unavailable."];
        }

        // Configuration details for the active gateway would be fetched here
        $config = GeneralSetting::getNested("gateways.$name");

        return match ($name) {
            'pay_on_delivery' => ['error' => false, 'gate' => new PayOnDeliveryGateway()],
            'paystack' => ['error' => false, 'gate' => new PaystackGateway($config)],
            'flutterwave' => ['error' => false, 'gate' => new FlutterwaveGateway($config)],
            default => ['error' => true, "message" => "Unsupported gateway: {$name}"]
        };
    }
}




// class PaymentManager
// {
//     protected $gateways;

//     public function __construct()
//     {
//         $this->gateways = GeneralSetting::get('gateways');
//     }

//     public function gateway(string $name)
//     {
//         // Check if the gateway exists and is active
//         if (!isset($this->gateways[$name]) || $this->gateways[$name]['status'] !== 'active') {
//             return ['error' => true, "message" => ucfirst($name) . " is unavailable."];
//         }

//         // Configuration details for the active gateway would be fetched here
//         // For demonstration, we assume they are fetched separately
//         $config = GeneralSetting::getNested("gateways.$name");

//         return match ($name) {
//             'paystack' => ['error' => false, 'gate' => new PaystackGateway($config)],
//             'flutterwave' => ['error' => false, 'gate' => new FlutterwaveGateway($config)],
//             default => ['error' => true, "message" => "Unsupported gateway: {$name}"]
//         };
//     }
// }
