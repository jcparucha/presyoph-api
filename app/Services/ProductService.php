<?php

namespace App\Services;

use App\Actions\Products\FirstOrCreateProductAction;
use App\Actions\Products\UpdateProductAction;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Traits\AssertionTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProductRepository $repo,
        private UpdateProductAction $updateAction,
        private FirstOrCreateProductAction $focAction,
    ) {}

    public function all(?int $perPage = 20): LengthAwarePaginator
    {
        return Product::with($this->repo->eagerLoadRelationships())->paginate($perPage, ['*'], 'page');
    }

    public function create(array $data): Product
    {
        return $this->focAction->handle($data);
    }

    public function show(Product $product): Product
    {
        // eager load connections
        return $product->load($this->repo->eagerLoadRelationships());
    }

    public function update(array $inputs, Product $product): Product
    {
        return $this->updateAction->handle($product, $inputs);
    }
}
