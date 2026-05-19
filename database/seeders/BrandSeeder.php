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
        DB::table('brands')->insert([
            ['id' => 1, 'name' => 'N/A', 'slug' => 'na'],
            ['id' => 2, 'name' => '555', 'slug' => '555'],
            ['id' => 3, 'name' => 'Alaska', 'slug' => 'alaska'],
            ['id' => 4, 'name' => 'Argentina', 'slug' => 'argentina'],
            ['id' => 5, 'name' => 'Bear Brand', 'slug' => 'bear-brand'],
            ['id' => 6, 'name' => 'CDO', 'slug' => 'cdo'],
            ['id' => 7, 'name' => 'Century Tuna', 'slug' => 'century-tuna'],
            ['id' => 8, 'name' => 'Datu Puti', 'slug' => 'datu-puti'],
            ['id' => 9, 'name' => 'Del Monte', 'slug' => 'del-monte'],
            ['id' => 10, 'name' => 'Eden', 'slug' => 'eden'],
            ['id' => 11, 'name' => 'Gardenia', 'slug' => 'gardenia'],
            ['id' => 12, 'name' => 'Great Taste', 'slug' => 'great-taste'],
            ['id' => 13, 'name' => 'Kopiko', 'slug' => 'kopiko'],
            ['id' => 14, 'name' => 'Lucky Me!', 'slug' => 'lucky-me!'],
            ['id' => 15, 'name' => 'Milo', 'slug' => 'milo'],
            ['id' => 16, 'name' => 'Nescafe', 'slug' => 'nescafe'],
            ['id' => 17, 'name' => 'Payless', 'slug' => 'payless'],
            ['id' => 18, 'name' => 'Purefoods', 'slug' => 'purefoods'],
            ['id' => 19, 'name' => 'San Miguel', 'slug' => 'san-miguel'],
            ['id' => 20, 'name' => 'Silver Swan', 'slug' => 'silver-swan'],
        ]);
    }
}
