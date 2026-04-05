<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("categories")->insert([
            ["id" => 1, "name" => "Baby Needs", "description" => "Milk formula, baby food, diapers, wipes, etc."],
            ["id" => 2, "name" => "Beverages", "description" => "Soft drinks, juices, coffee, tea, bottled water, etc."],
            ["id" => 3, "name" => "Bread & Bakery", "description" => "Pandesal, loaf bread, cakes, pastries, etc."],
            ["id" => 4, "name" => "Canned Goods", "description" => "Sardines, tuna, corned beef, beans, soups, etc."],
            ["id" => 5, "name" => "Condiments & Sauces", "description" => "Soy sauce, vinegar, ketchup, mayonnaise, oil, etc."],
            ["id" => 6, "name" => "Dairy", "description" => "Milk, cheese, butter, yogurt, etc."],
            ["id" => 7, "name" => "Fish & Seafood", "description" => "Fresh fish, shrimp, crabs, shellfish, etc."],
            ["id" => 8, "name" => "Frozen Foods", "description" => "Frozen meats, dumplings, veggies, ice cream, etc."],
            ["id" => 9, "name" => "Fruits", "description" => "Bananas, apples, mangoes, citrus, etc."],
            ["id" => 10, "name" => "Health & Wellness", "description" => "Vitamins, supplements, protein powders, etc."],
            ["id" => 11, "name" => "Household Essentials", "description" => "Detergent, dishwashing liquid, tissue, mops, etc."],
            ["id" => 12, "name" => "Kitchenware", "description" => "Pots, pans, utensils, food containers, etc."],
            ["id" => 13, "name" => "Meat", "description" => "Beef, pork, goat, fresh cuts, etc."],
            ["id" => 14, "name" => "Miscellaneous", "description" => "Other items"],
            ["id" => 15, "name" => "Personal Care", "description" => "Soap, shampoo, toothpaste, skincare, etc."],
            ["id" => 16, "name" => "Pet Supplies", "description" => "Dog food, cat food, treats, grooming items, etc."],
            ["id" => 17, "name" => "Poultry & Eggs", "description" => "Chicken, duck, turkey, eggs, etc."],
            ["id" => 18, "name" => "Processed Meats", "description" => "Hotdogs, sausages, ham, bacon, etc."],
            ["id" => 19, "name" => "Rice & Grains", "description" => "Rice, corn, oats, pasta, noodles, etc."],
            ["id" => 20, "name" => "School Supplies", "description" => "Notebooks, pens, pencils, paper, etc."],
            ["id" => 21, "name" => "Snacks", "description" => "Chips, biscuits, chocolates, nuts, etc."],
            ["id" => 22, "name" => "Spices & Seasonings", "description" => "Salt, pepper, herbs, etc."],
            ["id" => 23, "name" => "Vegetables", "description" => "Leafy greens, root crops, peppers, onions, etc."],
        ]);
    }
}
