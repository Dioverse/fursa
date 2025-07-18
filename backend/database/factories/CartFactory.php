<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // user_id is nullable, so sometimes link to a user, sometimes leave null
            'user_id' => $this->faker->boolean(70) ? User::factory() : null,
            // session_id is nullable for authenticated users, or for guests if user_id is null
            // Ensure unique session_id if it's not null
            'session_id' => $this->faker->boolean(50) ? Str::uuid()->toString() : null,
        ];
    }
}
