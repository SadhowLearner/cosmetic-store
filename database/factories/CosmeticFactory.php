<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Cosmetic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cosmetic>
 */
class CosmeticFactory extends Factory
{
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(2, true),
            'thumbnail' => fake()->imageUrl(),
            'about' => fake()->paragraph(),
            'price' => fake()->numberBetween(50000, 500000),
            'stock' => fake()->numberBetween(1, 100),
            'is_popular' => fake()->boolean(),
        ];
    }
}
