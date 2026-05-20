<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Baby Needs',
                'slug' => 'baby-needs',
                'description' => 'Milk formula, baby food, diapers, wipes, etc.',
            ],
            [
                'id' => 2,
                'name' => 'beverages',
                'slug' => 'beverages',
                'description' => 'Soft drinks, juices, coffee, tea, bottled water, etc.',
            ],
            [
                'id' => 3,
                'name' => 'Bread and Bakery',
                'slug' => 'bread-and-bakery',
                'description' => 'Pandesal, loaf bread, cakes, pastries, etc.',
            ],
            [
                'id' => 4,
                'name' => 'Canned Goods',
                'slug' => 'canned-goods',
                'description' => 'Sardines, tuna, corned beef, beans, soups, etc.',
            ],
            [
                'id' => 5,
                'name' => 'Condiments and Sauces',
                'slug' => 'condiments-and-sauces',
                'description' => 'Soy sauce, vinegar, ketchup, mayonnaise, oil, etc.',
            ],
            [
                'id' => 6,
                'name' => 'Dairy',
                'slug' => 'dairy',
                'description' => 'Milk, cheese, butter, yogurt, etc.',
            ],
            [
                'id' => 7,
                'name' => 'Fish and Seafood',
                'slug' => 'fish-and-seafood',
                'description' => 'Fresh fish, shrimp, crabs, shellfish, etc.',
            ],
            [
                'id' => 8,
                'name' => 'Frozen Foods',
                'slug' => 'frozen-foods',
                'description' => 'Frozen meats, dumplings, veggies, ice cream, etc.',
            ],
            [
                'id' => 9,
                'name' => 'Fruits',
                'slug' => 'fruits',
                'description' => 'Bananas, apples, mangoes, citrus, etc.',
            ],
            [
                'id' => 10,
                'name' => 'Health and Wellness',
                'slug' => 'health-and-wellness',
                'description' => 'Vitamins, supplements, protein powders, etc.',
            ],
            [
                'id' => 11,
                'name' => 'Household Essentials',
                'slug' => 'household-essentials',
                'description' => 'Detergent, dishwashing liquid, tissue, mops, etc.',
            ],
            [
                'id' => 12,
                'name' => 'Kitchenware',
                'slug' => 'kitchenware',
                'description' => 'Pots, pans, utensils, food containers, etc.',
            ],
            [
                'id' => 13,
                'name' => 'Meat',
                'slug' => 'meat',
                'description' => 'Beef, pork, goat, fresh cuts, etc.',
            ],
            [
                'id' => 14,
                'name' => 'Miscellaneous',
                'slug' => 'miscellaneous',
                'description' => 'Other items',
            ],
            [
                'id' => 15,
                'name' => 'Personal Care',
                'slug' => 'personal-care',
                'description' => 'Soap, shampoo, toothpaste, skincare, etc.',
            ],
            [
                'id' => 16,
                'name' => 'Pet Supplies',
                'slug' => 'pet-supplies',
                'description' => 'Dog food, cat food, treats, grooming items, etc.',
            ],
            [
                'id' => 17,
                'name' => 'Poultry and Eggs',
                'slug' => 'poultry-and-eggs',
                'description' => 'Chicken, duck, turkey, eggs, etc.',
            ],
            [
                'id' => 18,
                'name' => 'Processed Meats',
                'slug' => 'processed-meats',
                'description' => 'Hotdogs, sausages, ham, bacon, etc.',
            ],
            [
                'id' => 19,
                'name' => 'Rice and Grains',
                'slug' => 'rice-and-grains',
                'description' => 'Rice, corn, oats, pasta, noodles, etc.',
            ],
            [
                'id' => 20,
                'name' => 'School Supplies',
                'slug' => 'school-supplies',
                'description' => 'Notebooks, pens, pencils, paper, etc.',
            ],
            [
                'id' => 21,
                'name' => 'Snacks',
                'slug' => 'snacks',
                'description' => 'Chips, biscuits, chocolates, nuts, etc.',
            ],
            [
                'id' => 22,
                'name' => 'Spices and Seasonings',
                'slug' => 'spices-and-seasonings',
                'description' => 'Salt, pepper, herbs, etc.',
            ],
            [
                'id' => 23,
                'name' => 'Vegetables',
                'slug' => 'vegetables',
                'description' => 'Leafy greens, root crops, peppers, onions, etc.',
            ],
        ]);
    }
}
