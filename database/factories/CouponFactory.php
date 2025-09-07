<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->bothify('Coupon###')),
            'type' => fake()->randomElement(['percent', 'fixed']),
            'value' => fake()->randomFloat(2, 5, 50),
            'expires_at' => now()->addDays(30),
            'max_usage' => 100,
            'used_count' => 0,
        ];
    }
}
