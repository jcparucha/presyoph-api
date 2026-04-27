<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductHandlerInterface
{
    public function all(int $perPage): LengthAwarePaginator;
    public function create(array $data): product;
    public function get(Product $product): Product;
    public function update(array $data, Product $product): Product;
    public function delete(string $id): void;
}
