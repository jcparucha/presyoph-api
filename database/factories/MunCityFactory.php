<?php

namespace Database\Factories;

use App\Models\MunCity;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MunCity>
 */
class MunCityFactory extends Factory
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
            'code' => strval(fake()->numberBetween(1000000000, 9999999999)),
            'province_code' => Province::factory(),
        ];
    }
}
