<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\Product\NewProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\AssertionTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    use AssertionTrait;

    public function __construct(private ProductService $productService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(PaginationRequest $request): JsonResource
    {
        return ProductResource::collection(
            $this->productService->all($request->per_page),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewProductRequest $request): JsonResponse
    {
        try {
            $newProduct = $this->productService->create($request->all());

            $this->assertShouldNotBeNull($newProduct);

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
    public function show(Product $product): JsonResource
    {
        return $this->productService->show($product)->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateProductRequest $request,
        Product $product,
    ): JsonResource {
        return $this->productService
            ->update($request->validated(), $product)
            ->toResource();
    }
}
