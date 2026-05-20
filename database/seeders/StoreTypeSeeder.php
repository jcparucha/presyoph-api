<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('store_types')->insert([
            ['id' => 1, 'name' => 'Baby Store', 'slug' => 'baby-store'],
            ['id' => 2, 'name' => 'Bakery', 'slug' => 'bakery'],
            [
                'id' => 3,
                'name' => 'Convenience Store',
                'slug' => 'convenience-store',
            ],
            ['id' => 4, 'name' => 'Fish Market', 'slug' => 'fish-market'],
            ['id' => 5, 'name' => 'Fruit Stand', 'slug' => 'fruit-stand'],
            [
                'id' => 6,
                'name' => 'General Merchandise',
                'slug' => 'general-merchandise',
            ],
            ['id' => 7, 'name' => 'Grocery Store', 'slug' => 'grocery-store'],
            ['id' => 8, 'name' => 'Hardware Store', 'slug' => 'hardware-store'],
            ['id' => 9, 'name' => 'Meat Shop', 'slug' => 'meat-shop'],
            ['id' => 10, 'name' => 'Pet Shop', 'slug' => 'pet-shop'],
            ['id' => 11, 'name' => 'Pharmacy', 'slug' => 'pharmacy'],
            ['id' => 12, 'name' => 'Public Market', 'slug' => 'public-market'],
            [
                'id' => 13,
                'name' => 'Sari-Sari Store',
                'slug' => 'sari-sari-store',
            ],
            ['id' => 14, 'name' => 'Supermarket', 'slug' => 'supermarket'],
            [
                'id' => 15,
                'name' => 'Vegetable Stall',
                'slug' => 'vegetable-stall',
            ],
        ]);
    }
}
