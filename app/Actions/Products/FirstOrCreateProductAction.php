<?php

namespace App\Actions\Products;

use App\Actions\Brand\FirstOrCreateBrandAction;
use App\Actions\Category\FirstOrCreateCategoryAction;
use App\Actions\Establishment\FirstOrCreateEstablishmentAction;
use App\Actions\FirstOrCreatePriceAction;
use App\Models\Product;
use App\Models\Unit;
use App\Repositories\ProductRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use App\Traits\AssertionTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FirstOrCreateProductAction
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProductRepository $repo,
        private UserRepository $userRepo,
        private TagRepository $tagRepo,
        private FirstOrCreateBrandAction $focBrandAction,
        private FirstOrCreateCategoryAction $focCategoryAction,
        private FirstOrCreateEstablishmentAction $focEstablishmentAction,
        private FirstOrCreatePriceAction $focPriceAction,
    ) {}

    public function handle(array $data): ?Product
    {
        try {
            // product properties
            $this->assertShouldHaveKeys(
                ['name', 'weight', 'unit', 'brand', 'category', 'establishment', 'price', 'tags'],
                $data,
            );

            return DB::transaction(function () use ($data) {
                $unit = Unit::ofUnit($data['unit'])->first();

                $brand = $this->focBrandAction->handle(['name' => $data['brand']]);

                $category = $this->focCategoryAction->handle([
                    ...$data['category'],
                    'description' => $data['category']['description'] ?? '',
                ]);

                $product = $this->repo->firstOrCreate($this->userRepo->getAuthUser(), [
                    'name' => $data['name'],
                    'weight' => $data['weight'],
                    'unit_id' => $unit->id,
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                ]);

                $establishment = $this->focEstablishmentAction->handle($data['establishment']);

                // only create initial price for new product on an establishment
                $this->focPriceAction->handle([
                    'product_id' => $product->id,
                    'establishment_id' => $establishment->id,
                    'price' => $data['price'],
                ]);

                // only create product tags if it's a new product
                if ($product->wasRecentlyCreated) {
                    $this->tagRepo->syncTags($product, $data['tags']);
                }

                return $product;
            })->load($this->repo->eagerLoadRelationships());
        } catch (QueryException $e) {
            Log::error(__CLASS__.': Database error - '.$e->getMessage());

            throw new \RuntimeException('Something went wrong on saving the product.', 0, $e);
        }
    }
}
