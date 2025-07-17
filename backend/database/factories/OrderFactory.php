<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'shipping_address_id' => \App\Models\ShippingAddress::factory(),
            'order_id' => Str::uuid()->toString(),
            'total_amount' => fake()->randomFloat(2, 1000, 10000),
            'status' => fake()->randomElement(['pending', 'shipped', 'out for delivery', 'delivered', 'cancelled']),
        ];
    }
}
