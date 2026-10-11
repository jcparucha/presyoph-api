<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;

class ProductRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function firstOrCreate(User $user, array $data): Product
    {
        return Product::query()->firstOrCreate(
            [
                'name' => $data['name'],
                'weight' => $data['weight'],
                'unit_id' => $data['unit_id'],
                'brand_id' => $data['brand_id'],
            ],
            [
                'added_by' => $user->id,
                'category_id' => $data['category_id'],
            ],
        );
    }

    public function update(Product $product, array $data): Product
    {
        $product->fill($data);
        $product->save();

        return $product;
    }

    public function eagerLoadRelationships(): array
    {
        return [
            'brand',
            'category',
            'tags',
            'unit',
            'user',
            'prices.establishment.barangay',
            'prices.establishment.storeType',
            'prices' => function ($query) {
                // ProductPrice scope
                $query->latestPerEstablishment();
            },
        ];
    }
}
