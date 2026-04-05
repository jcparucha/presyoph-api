<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("store_types")->insert([
            ["id" => 1, "name" => "Baby Store"],
            ["id" => 2, "name" => "Bakery"],
            ["id" => 3, "name" => "Convenience Store"],
            ["id" => 4, "name" => "Fish Market"],
            ["id" => 5, "name" => "Fruit Stand"],
            ["id" => 6, "name" => "General Merchandise"],
            ["id" => 7, "name" => "Grocery Store"],
            ["id" => 8, "name" => "Hardware Store"],
            ["id" => 9, "name" => "Meat Shop"],
            ["id" => 10, "name" => "Pet Shop"],
            ["id" => 11, "name" => "Pharmacy"],
            ["id" => 12, "name" => "Public Market"],
            ["id" => 13, "name" => "Sari-Sari Store"],
            ["id" => 14, "name" => "Supermarket"],
            ["id" => 15, "name" => "Vegetable Stall"],
        ]);
    }
}
