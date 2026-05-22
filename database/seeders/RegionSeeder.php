<?php

namespace Database\Seeders;

use App\Traits\Seeders\CanBatchSeedFromJson;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    use CanBatchSeedFromJson;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedFromFile(
            path: database_path('seeders/psgc_data/regions.json'),
            table: 'regions',
            uniqueBy: 'code',
            update: ['name'],
        );
    }
}
