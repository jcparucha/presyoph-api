<?php

namespace App\Services;

use App\Contracts\ProductHandlerInterface;
use App\Models\Product;
use App\Models\Unit;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductHandlerService implements ProductHandlerInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private BrandHandlerService $brandHandler,
        private CategoryHandlerService $categoryHandler,
        private EstablishmentHandlerService $establishmentHandler,
        private ProductPriceHandlerService $productPriceHandler,
        private TagHandlerService $tagHandler,
    ) {
        //
    }

    public function all(int $perPage = 20): LengthAwarePaginator
    {
        return Product::with([
            'brand',
            'category',
            'tags',
            'unit',
            'user',
            'prices',
        ])->paginate($perPage, ['*'], 'page');
    }

    public function create(array $data): Product
    {
        // TODO add transaction
        // try {
        // DB::transaction(function () use ($data, &$product) {
        $brand = $this->brandHandler->firstOrCreate($data['brand']);

        $category = $this->categoryHandler->firstOrCreate($data['category']);

        $unit = Unit::ofUnit($data['unit'])->first();

        // Verify if the product is already existed, else create new record w/ User
        $product = Product::firstOrCreate(
            [
                'name' => $data['name'],
                'net_weight' => $data['net_weight'],
                'unit_id' => $unit->id,
                'brand_id' => $brand->id,
                'category_id' => $category->id,
            ],
            ['added_by' => Auth::guard('web')->user()->id],
        );

        $newTags = $this->tagHandler->getNewTags($data['tags']);

        // only sync tags if have new tags
        if (count($newTags)) {
            $product->tags()->syncWithPivotValuesOrFail($newTags, [
                'created_at' => now(),
            ]);
        }

        // TODO add validation that the brgy_id is belonged to mun_city_id, and mun_city_id to province_id, and to region_id
        $establishment = $this->establishmentHandler->firstOrCreate(
            $data['establishment'],
        );

        $this->productPriceHandler->firstOrCreate(
            $product->id,
            $establishment->id,
            $data['price'],
        );

        return $product->load([
            'brand',
            'category',
            'tags',
            'unit',
            'user',
            'prices',
        ]);
    }

    //TODO - NOTE NEEDED?
    public function get(Product $product): Product
    {
        // eager load connections
        return $product->load([
            'brand',
            'category',
            'tags',
            'unit',
            'user',
            'prices',
        ]);
    }

    // TODO update value of product only!
    public function update(array $data, Product $product): Product
    {
        return new Product();
    }

    public function delete(string $id): void
    {
        return;
    }
}
