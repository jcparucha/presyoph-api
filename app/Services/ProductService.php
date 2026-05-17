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

class ProductService
{
    use AssertionTrait;

    private $fields = ['name', 'weight', 'unit_id', 'brand_id'];

    private $eagerLoad = [
        'brand',
        'category',
        'tags',
        'unit',
        'user',
        'prices.establishment.barangay',
        'prices.establishment.storeType',
    ];

    /**
     * Create a new class instance.
     */
    public function __construct(
        private BrandService $brandService,
        private CategoryService $categoryService,
        private EstablishmentService $establishmentService,
        private ProductPriceService $productPriceService,
        private TagService $tagService,
    ) {
        //
    }

    public function all(?int $perPage = 20): LengthAwarePaginator
    {
        return Product::with([
            ...$this->eagerLoad,
            'prices' => function ($query) {
                $query->latestPerEstablishment();
            },
        ])->paginate($perPage, ['*'], 'page');
    }

    public function create(array $data): Product
    {
        try {
            $product = null;

            DB::transaction(function () use ($data, &$product) {
                $brand = $this->brandService->firstOrCreate([
                    'name' => $data['brand'],
                ]);

                $category = $this->categoryService->firstOrCreate(
                    $data['category'],
                );

                $unit = Unit::ofUnit($data['unit'])->first();

                $product = Product::firstOrCreate(
                    [
                        'name' => $data['name'],
                        'weight' => $data['weight'],
                        'unit_id' => $unit->id,
                        'brand_id' => $brand->id,
                    ],
                    [
                        'category_id' => $category->id,
                        'added_by' => Auth::guard('web')->user()->id,
                    ],
                );

                $establishment = $this->establishmentService->firstOrCreate(
                    $data['establishment'],
                );

                // only create initial price for new product on an establishment
                // to add/update a price, use POST product/{product}/price or PATCH product/{product}/price/{productprice}
                $this->productPriceService->firstOrCreate([
                    'product_id' => $product->id,
                    'establishment_id' => $establishment->id,
                    'price' => $data['price'],
                ]);

                // only create product tags if it's a new product, else use POST product/{product}/tags to update tags
                if ($product->wasRecentlyCreated) {
                    $this->tagService->syncTags($product, $data['tags']);
                }
            });

            return !is_null($product)
                ? $product->load([
                    ...$this->eagerLoad,
                    'prices' => function ($query) {
                        $query->latestPerEstablishment();
                    },
                ])
                : null;
        } catch (Exception $error) {
            throw new Exception($error->getMessage(), 500);
        }
    }

    public function show(Product $product): Product
    {
        // eager load connections
        return $product->load([
            ...$this->eagerLoad,
            'prices' => function ($query) {
                $query->latestPerEstablishment();
            },
        ]);
    }

    public function update(array $inputs, Product $product): Product
    {
        $data = $this->generateProductData($product, $inputs);

        $this->validateIfUniqueProduct($data);

        $this->updateProduct($product, $data);

        // call refresh() to re-hydrate the product
        return $product->refresh();
    }

    public function delete(string $id): void
    {
        return;
    }

    protected function generateProductData(
        Product $product,
        array $inputs,
    ): array {
        $unit = isset($inputs['unit'])
            ? Unit::ofUnit($inputs['unit'])->first()->id
            : $product->unit_id;

        $brand = isset($inputs['brand'])
            ? $this->brandService->firstOrCreate(['name' => $inputs['brand']])
                ->id
            : $product->brand_id;

        $category = isset($inputs['category'])
            ? $this->categoryService->firstOrCreate([
                'description' => '',
                ...$inputs['category'],
            ])->id
            : $product->category_id;

        return [
            'id' => $product->id,
            'name' => $inputs['name'] ?? $product->name,
            'weight' => intval($inputs['weight']) ?? $product->weight,
            'unit_id' => $unit,
            'brand_id' => $brand,
            'category_id' => $category,
        ];
    }

    /**
     * Check if the changes being made in the product is already exists
     *
     * A product should be unique by its Brand, Name, Net Weight, and Unit
     *
     * @param array $data
     * @return void
     */
    protected function validateIfUniqueProduct(array $data): void
    {
        $this->assertShouldHaveKeys(['id', ...$this->fields], $data);

        $product = Product::whereNot('id', $data['id'])
            ->where('name', $data['name'])
            ->where('weight', $data['weight'])
            ->where('unit_id', $data['unit_id'])
            ->where('brand_id', $data['brand_id'])
            ->first();

        if (!is_null($product)) {
            throw ValidationException::withMessages([
                'product' => __('validation.unique', [
                    'attribute' => 'product',
                ]),
            ]);
        }
    }

    /**
     * @param Product $product
     * @param array $data
     * @return void
     */
    protected function updateProduct(Product $product, array $data): void
    {
        $fields = ['id', 'category_id', ...$this->fields];

        $this->assertShouldHaveKeys($fields, $data);

        $this->assertShouldBeInteger($data['weight']);

        foreach ($fields as $field) {
            if ($product->$field !== $data[$field]) {
                $product->$field = $data[$field];
            }
        }

        // save if have changes made
        if ($product->isDirty()) {
            $product->save();
        }
    }
}
