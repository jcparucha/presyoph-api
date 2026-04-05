<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("units")->insert([
            ["id" => 1, "name" => "Box", "abbreviation" => "box"],
            ["id" => 2, "name" => "Bottle", "abbreviation" => "bottle"],
            ["id" => 3, "name" => "Bundle", "abbreviation" => "bundle"],
            ["id" => 4, "name" => "Can", "abbreviation" => "can"],
            ["id" => 5, "name" => "Gram", "abbreviation" => "g"],
            ["id" => 6, "name" => "Kilogram", "abbreviation" => "kg"],
            ["id" => 7, "name" => "Liter", "abbreviation" => "L"],
            ["id" => 8, "name" => "Mililiter", "abbreviation" => "mL"],
            ["id" => 9, "name" => "Pack", "abbreviation" => "pack"],
            ["id" => 10, "name" => "Piece", "abbreviation" => "pc"],
            ["id" => 11, "name" => "Tray", "abbreviation" => "tray"],
        ]);
    }
}
