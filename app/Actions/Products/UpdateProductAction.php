<?php

namespace App\Actions\Products;

use App\Actions\Brand\FirstOrCreateBrandAction;
use App\Actions\Category\FirstOrCreateCategoryAction;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Unit;
use App\Repositories\ProductRepository;
use App\Traits\AssertionTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UpdateProductAction
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProductRepository $repo,
        private FirstOrCreateBrandAction $focBrandAction,
        private FirstOrCreateCategoryAction $focCategoryAction,
    ) {}

    public function handle(Product $product, array $inputs): ?Product
    {
        try {
            return DB::transaction(function () use ($product, $inputs) {
                $data = $this->generateProductData($product, $inputs);

                $this->validateIfUniqueProduct($product, $data);

                // call refresh() to re-hydrate the product
                return $this->repo->update($product, $data)->load('user')->refresh();
            });
        } catch (QueryException $e) {
            Log::error(__CLASS__.': Database error - '.$e->getMessage());

            // TODO: Should throw exception error
            throw new \RuntimeException('Something went wrong on saving the product.', 0, $e);
        }
    }

    protected function generateProductData(Product $product, array $inputs): array
    {
        $unit = isset($inputs['unit']) ? Unit::ofUnit($inputs['unit'])->first()->id : $product->unit_id;

        $brand = isset($inputs['brand'])
            ? $this->focBrandAction->handle(['name' => $inputs['brand']])->id
            : $product->brand_id;

        $category = isset($inputs['category'])
            ? $this->focCategoryAction->handle([
                'description' => '',
                ...$inputs['category'],
            ])->id
            : $product->category_id;

        $name = $inputs['name'] ?? $product->name;

        $weight = isset($inputs['weight']) ? intval($inputs['weight']) : $product->weight;

        return [
            'name' => $name,
            'weight' => $weight,
            'unit_id' => $unit,
            'brand_id' => $brand,
            'category_id' => $category,
        ];
    }

    /**
     * Check if the changes being made in the product is already exists
     *
     * A product should be unique by its Brand, Name, Weight, and Unit
     */
    protected function validateIfUniqueProduct(Product $product, array $data): void
    {
        $this->assertShouldHaveKeys(['name', 'weight', 'unit_id', 'brand_id', 'category_id'], $data);

        $existingProduct = Product::whereNot('id', $product->id)
            ->where('name', $data['name'])
            ->where('weight', $data['weight'])
            ->where('unit_id', $data['unit_id'])
            ->where('brand_id', $data['brand_id'])
            ->first();

        if (! is_null($existingProduct)) {
            throw ValidationException::withMessages([
                'system' => __('validation.custom.validation.unique_product'),
            ]);
        }
    }
}
