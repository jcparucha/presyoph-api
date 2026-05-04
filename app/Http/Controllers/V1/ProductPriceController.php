<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\ProductPricePaginationRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Product;
use App\Services\ProductPriceService;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    public function __construct(
        private ProductPriceService $productPriceService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(
        ProductPricePaginationRequest $request,
        Product $product,
    ) {
        return ProductPriceResource::collection(
            $this->productPriceService->all($request->validated(), $product),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaginationRequest $request, Product $product)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
