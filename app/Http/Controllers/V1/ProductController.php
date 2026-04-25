<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewProductRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductHandlerService;
use App\Traits\AssertionTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * SECTION Product
 *
 * NOTE flow
 * - Product can't be deleted
 * - What to update on Product? (Brand, Name, Net Weight, Unit, Category)
 *
 * TODO todo
 * - ✅ Product Price not yet final on the Product resource
 * - ✅ update product
 * - ✅ add DB Transaction to the creation/updating of product
 * - add validation for Product Create and Update✅
 * - should remove 'handler' on file name
 */

class ProductController extends Controller
{
    use AssertionTrait;

    public function __construct(
        private ProductHandlerService $productHandler,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(PaginationRequest $request): JsonResource
    {
        // TODO per_page ?? 5 is for testing only, change to 20
        return ProductResource::collection(
            $this->productHandler->all($request->per_page ?? 5),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $newProduct = $this->productHandler->create($request->all());

            $this->assertShouldBeNotNull($newProduct);

            $newResourceLink = route('products.show', [
                'product' => $newProduct->id,
            ]);

            return $newProduct
                ->toResource()
                ->additional(['links' => ['related' => $newResourceLink]])
                ->response()
                ->header('Location', $newResourceLink)
                ->setStatusCode($newProduct->wasRecentlyCreated ? 201 : 200);
        } catch (Exception $error) {
            return response()->json(
                ['message' => $error->getMessage()],
                $error->getCode(),
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // return $this->productHandler->get($product);
        return $this->productHandler->get($product)->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewProductRequest $request, Product $product)
    {
        return $this->productHandler
            ->update($request->validated(), $product)
            ->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Request $request, Product $product)
    // {
    //     return response()->json(['message' => 'Deleted successfully.'], 204);
    // }
}
