<?php

namespace App\Services;

use App\Actions\Products\UpdateProductAction;
use App\Models\Product;
use App\Models\Unit;
use App\Traits\AssertionTrait;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService
{
    use AssertionTrait;

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
        private UpdateProductAction $updateAction
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
            $product = DB::transaction(function () use ($data) {
                $brand = $this->brandService->firstOrCreate([
                    'name' => $data['brand'],
                ]);

                $category = $this->categoryService->firstOrCreate($data['category']);

                $unit = Unit::ofUnit($data['unit'])->first();

                $user = Auth::guard('web')->user();

                $product = $user->products()->firstOrCreate(
                    [
                        'name' => $data['name'],
                        'weight' => $data['weight'],
                        'unit_id' => $unit->id,
                        'brand_id' => $brand->id,
                    ],
                    ['category_id' => $category->id],
                );

                $establishment = $this->establishmentService->firstOrCreate($data['establishment']);

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

                return $product;
            });

            return $product->load([
                ...$this->eagerLoad,
                'prices' => function ($query) {
                    $query->latestPerEstablishment();
                },
            ]);
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
        return $this->updateAction->handle($product, $inputs);
    }
}
