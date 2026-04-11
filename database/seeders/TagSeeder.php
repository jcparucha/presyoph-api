<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tags')->insert([
            ['id' => 1, 'name' => 'baby'],
            ['id' => 2, 'name' => 'beverage'],
            ['id' => 3, 'name' => 'bulk'],
            ['id' => 4, 'name' => 'canned'],
            ['id' => 5, 'name' => 'cleaning'],
            ['id' => 6, 'name' => 'condiment'],
            ['id' => 7, 'name' => 'dairy'],
            ['id' => 8, 'name' => 'fresh'],
            ['id' => 9, 'name' => 'frozen'],
            ['id' => 10, 'name' => 'fruit'],
            ['id' => 11, 'name' => 'hygiene'],
            ['id' => 12, 'name' => 'instant'],
            ['id' => 13, 'name' => 'kitchen'],
            ['id' => 14, 'name' => 'meat'],
            ['id' => 15, 'name' => 'perishable'],
            ['id' => 16, 'name' => 'pet'],
            ['id' => 17, 'name' => 'ready-to-eat'],
            ['id' => 18, 'name' => 'seafood'],
            ['id' => 19, 'name' => 'school'],
            ['id' => 20, 'name' => 'seasoning'],
            ['id' => 21, 'name' => 'snack'],
            ['id' => 22, 'name' => 'staple food'],
            ['id' => 23, 'name' => 'vegetable'],
        ]);
    }
}
