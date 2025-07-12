<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            // 'category_id' => \App\Models\Category::factory(),
            'name' => $name,
            'short_description' => fake()->sentence,
            'description' => fake()->paragraph,
            'base_price' => fake()->randomFloat(2, 100, 1000),
            'category' => fake()->word,
            'image' => fake()->image(word:$name),
            'stock_quantity' => fake()->numberBetween(10, 100),
            'low_stock_threshold' => 5,
            'tags' => json_encode(fake()->words(3)),
        ];
    }
}
