<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function update(Product $product, array $data): Product
    {
        $product->fill($data);
        $product->save();

        return $product;
    }
}
