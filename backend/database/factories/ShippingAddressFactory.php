<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingAddress>
 */
class ShippingAddressFactory extends Factory
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
            'full_name' => fake()->name,
            'phone' => fake()->phoneNumber,
            'address_line1' => fake()->streetAddress,
            'address_line2' => fake()->secondaryAddress,
            'city' => fake()->city,
            'state' => fake()->state,
            'postal_code' => fake()->postcode,
            'country' => 'Nigeria',
            'is_default' => fake()->boolean(30),
        ];
    }
}
