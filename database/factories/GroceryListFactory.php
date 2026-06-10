<?php

namespace Database\Factories;

use App\Models\GroceryList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GroceryList>
 */
class GroceryListFactory extends Factory
{
    protected ?string $name;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => $name,
            'slug' => generate_unique_slug($name),
            'description' => fake()->text(),
            'is_public' => false,
        ];
    }

    /**
     * Indicates that the grocery lists are published
     */
    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return ['is_public' => true];
        });
    }
}
