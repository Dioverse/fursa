<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'user_id' => \App\Models\User::factory(),
            'status' => fake()->randomElement(['pending', 'successful', 'failed']),
            'payment_gateway' => fake()->randomElement(['Paystack', 'Flutterwave']),
            'payment_method' => fake()->randomElement(['card', 'transfer']),
            'transaction_reference' => strtoupper(fake()->bothify('TXN###??')),
            'amount' => fake()->randomFloat(2, 1000, 10000),
            'paid_at' => now(),
        ];
    }
}
