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
            ['id' => 1, 'name' => 'baby', 'slug' => 'baby'],
            ['id' => 2, 'name' => 'beverage', 'slug' => 'beverage'],
            ['id' => 3, 'name' => 'bulk', 'slug' => 'bulk'],
            ['id' => 4, 'name' => 'canned', 'slug' => 'canned'],
            ['id' => 5, 'name' => 'cleaning', 'slug' => 'cleaning'],
            ['id' => 6, 'name' => 'condiment', 'slug' => 'condiment'],
            ['id' => 7, 'name' => 'dairy', 'slug' => 'dairy'],
            ['id' => 8, 'name' => 'fresh', 'slug' => 'fresh'],
            ['id' => 9, 'name' => 'frozen', 'slug' => 'frozen'],
            ['id' => 10, 'name' => 'fruit', 'slug' => 'fruit'],
            ['id' => 11, 'name' => 'hygiene', 'slug' => 'hygiene'],
            ['id' => 12, 'name' => 'instant', 'slug' => 'instant'],
            ['id' => 13, 'name' => 'kitchen', 'slug' => 'kitchen'],
            ['id' => 14, 'name' => 'meat', 'slug' => 'meat'],
            ['id' => 15, 'name' => 'perishable', 'slug' => 'perishable'],
            ['id' => 16, 'name' => 'pet', 'slug' => 'pet'],
            ['id' => 17, 'name' => 'ready to eat', 'slug' => 'ready-to-eat'],
            ['id' => 18, 'name' => 'seafood', 'slug' => 'seafood'],
            ['id' => 19, 'name' => 'school', 'slug' => 'school'],
            ['id' => 20, 'name' => 'seasoning', 'slug' => 'seasoning'],
            ['id' => 21, 'name' => 'snack', 'slug' => 'snack'],
            ['id' => 22, 'name' => 'staple food', 'slug' => 'staple-food'],
            ['id' => 23, 'name' => 'vegetable', 'slug' => 'vegetable'],
        ]);
    }
}
