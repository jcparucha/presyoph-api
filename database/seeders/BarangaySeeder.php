<?php

namespace Database\Seeders;

use App\Traits\Seeders\CanBatchSeedFromJson;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    use CanBatchSeedFromJson;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedFromFile(
            path: database_path('seeders/psgc_data/barangays.json'),
            table: 'barangays',
            uniqueBy: 'code',
            update: ['name', 'mun_city_code'],
        );
    }
}
