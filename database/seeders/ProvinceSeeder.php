<?php

namespace Database\Seeders;

use App\Traits\Seeders\CanBatchSeedFromJson;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    use CanBatchSeedFromJson;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedFromFile(
            path: database_path('seeders/psgc_data/provinces.json'),
            table: 'provinces',
            uniqueBy: 'code',
            update: ['name', 'region_code'],
        );
    }
}
