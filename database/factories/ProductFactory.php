<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
            'name' => fake()->words(2, true),
            'weight' => fake()->numberBetween(1, 1000),
            'unit_id' => Unit::factory(),
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'added_by' => User::factory(),
        ];
    }
}
