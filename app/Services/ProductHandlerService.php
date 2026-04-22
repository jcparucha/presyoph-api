<?php

namespace App\Services;

use App\Traits\AssertionTrait;
use App\Models\Product;
use App\Models\Unit;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductHandlerService
{
    use AssertionTrait;

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
        try {
            $product = null;

            DB::transaction(function () use ($data, &$product) {
                $brand = $this->brandHandler->firstOrCreate([
                    'name' => $data['brand'],
                ]);

                $category = $this->categoryHandler->firstOrCreate(
                    $data['category'],
                );

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

                $this->productPriceHandler->firstOrCreate([
                    'product_id' => $product->id,
                    'establishment_id' => $establishment->id,
                    'price' => $data['price'],
                ]);
            });

            return !is_null($product)
                ? $product->load([
                    'brand',
                    'category',
                    'tags',
                    'unit',
                    'user',
                    'prices',
                ])
                : null;
        } catch (Exception $error) {
            throw new Exception($error->getMessage(), 500);
        }
    }

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

    public function update(array $inputs, Product $product): Product
    {
        $unit = isset($inputs['unit'])
            ? Unit::where('abbreviation', $inputs['unit'])->first()->id
            : $product->unit_id;

        $brand = isset($inputs['brand'])
            ? $this->brandHandler->firstOrCreate(['name' => $inputs['brand']])
                ->id
            : $product->brand_id;

        $category = isset($inputs['category'])
            ? $this->categoryHandler->firstOrCreate($inputs['category'])->id
            : $product->category_id;

        $data = [
            'id' => $product->id,
            'name' => $inputs['name'] ?? $product->name,
            'net_weight' =>
                intval($inputs['net_weight']) ?? $product->net_weight,
            'unit_id' => $unit,
            'brand_id' => $brand,
        ];

        $isProductAlreadyExists = $this->isProductAlreadyExists($data);

        if ($isProductAlreadyExists) {
            // TODO throw proper error message and HTTP code
            // > use ValidationException with 422 error
            throw new ValidationException('Product already exists.', 422);
        }

        $this->updateProduct($product, ['category_id' => $category, ...$data]);

        // call refresh() to re-hydrate the product
        return $product->refresh();
    }

    public function delete(string $id): void
    {
        return;
    }

    /**
     * Check if the changes being made in the product is already exists
     *
     * A product should be unique by its Brand, Name, Net Weight, and Unit
     *
     * @param array $data
     * @return boolean
     */
    protected function isProductAlreadyExists(array $data): bool
    {
        $this->assertShouldHaveKeys(
            ['id', 'name', 'net_weight', 'unit_id', 'brand_id'],
            $data,
        );

        $product = Product::whereNot('id', $data['id'])
            ->where('name', $data['name'])
            ->where('net_weight', $data['net_weight'])
            ->where('unit_id', $data['unit_id'])
            ->where('brand_id', $data['brand_id'])
            ->first();

        return !is_null($product);
    }

    /**
     * @param Product $product
     * @param array $data
     * @return void
     */
    protected function updateProduct(Product $product, array $data)
    {
        $this->assertShouldHaveKeys(
            ['id', 'name', 'net_weight', 'unit_id', 'brand_id', 'category_id'],
            $data,
        );

        $this->assertShouldBeInteger($data['net_weight']);

        if ($product->name !== $data['name']) {
            $product->name = $data['name'];
        }

        if ($product->net_weight !== $data['net_weight']) {
            $product->net_weight = $data['net_weight'];
        }

        if ($product->unit_id !== $data['unit_id']) {
            $product->unit_id = $data['unit_id'];
        }

        if ($product->brand_id !== $data['brand_id']) {
            $product->brand_id = $data['brand_id'];
        }

        if ($product->category_id !== $data['category_id']) {
            $product->category_id = $data['category_id'];
        }

        // save if have changes made
        if ($product->isDirty()) {
            $product->save();
        }
    }
}
