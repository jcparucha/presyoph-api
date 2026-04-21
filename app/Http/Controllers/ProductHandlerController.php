<?php

namespace App\Http\Controllers;

// use App\Contracts\ProductHandlerInterface;

use App\Contracts\ProductHandlerInterface;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Product;
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
 * - update product
 * - add DB Transaction to the creation/updating of product
 * - add validation
 */

class ProductHandlerController extends Controller
{
    public function __construct(
        private ProductHandlerInterface $productHandler,
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
        $newProduct = $this->productHandler->create($request->all());

        // TODO should send 201, with Product body and Link to product/new_product?
        return $newProduct
            ->toResource()
            ->additional([
                'links' => [
                    'related' => route('products.show', [
                        'product' => $newProduct->id,
                    ]),
                ],
            ])
            ->response()
            ->setStatusCode($newProduct->wasRecentlyCreated ? 201 : 200);
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
    public function update(Request $request, Product $product)
    {
        $this->productHandler->update($request->all(), $product);
        // return response()->json(['message' => 'Updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Request $request, Product $product)
    // {
    //     return response()->json(['message' => 'Deleted successfully.'], 204);
    // }
}
