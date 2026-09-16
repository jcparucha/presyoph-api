<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Establishment;
use App\Models\StoreType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Establishment>
 */
class EstablishmentFactory extends Factory
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
            'barangay_code' => Barangay::factory(),
            'store_type_id' => StoreType::factory(),
            'added_by' => User::factory(),
        ];
    }
}
