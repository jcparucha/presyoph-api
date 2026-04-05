<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("brands")->insert([
            ["id" => 1, "name" => "N/A"],
            ["id" => 2, "name" => "555"],
            ["id" => 3, "name" => "Alaska"],
            ["id" => 4, "name" => "Argentina"],
            ["id" => 5, "name" => "Bear Brand"],
            ["id" => 6, "name" => "CDO"],
            ["id" => 7, "name" => "Century Tuna"],
            ["id" => 8, "name" => "Datu Puti"],
            ["id" => 9, "name" => "Del Monte"],
            ["id" => 10, "name" => "Eden"],
            ["id" => 11, "name" => "Gardenia"],
            ["id" => 12, "name" => "Great Taste"],
            ["id" => 13, "name" => "Kopiko"],
            ["id" => 14, "name" => "Lucky Me!"],
            ["id" => 15, "name" => "Milo"],
            ["id" => 16, "name" => "Nescafe"],
            ["id" => 17, "name" => "Payless"],
            ["id" => 18, "name" => "Purefoods"],
            ["id" => 19, "name" => "San Miguel"],
            ["id" => 20, "name" => "Silver Swan"],
        ]);
    }
}
