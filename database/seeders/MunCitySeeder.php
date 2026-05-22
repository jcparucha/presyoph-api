<?php

namespace Database\Seeders;

use App\Traits\Seeders\CanBatchSeedFromJson;
use Illuminate\Database\Seeder;

class MunCitySeeder extends Seeder
{
    use CanBatchSeedFromJson;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedFromFile(
            path: database_path('seeders/psgc_data/mun_cities.json'),
            table: 'mun_cities',
            uniqueBy: 'code',
            update: ['name', 'province_code'],
        );
    }
}
