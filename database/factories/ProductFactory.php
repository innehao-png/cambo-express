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
        return [
            'name' => $this->faker->words(3, true),
            'category_id' => \App\Models\Category::inRandomOrder()->first()?->id ?? 1,
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 2000),
            'stock' => $this->faker->numberBetween(0, 500),
            'discount' => $this->faker->optional(0.3, 0)->randomFloat(2, 5, 50),
            'status' => 'active',
            'image' => null,
        ];
    }
}
