<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPrice\NewProductPriceRequest;
use App\Http\Requests\ProductPrice\PaginationRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\ProductPriceService;

class ProductPriceController extends Controller
{
    public function __construct(
        private ProductPriceService $productPriceService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(PaginationRequest $request, Product $product)
    {
        return ProductPriceResource::collection(
            $this->productPriceService->all($request->validated(), $product),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewProductPriceRequest $request, Product $product)
    {
        $productPrice = $this->productPriceService->create(
            $request->validated(),
            $product,
        );

        $newResourceLink = route('products.prices.show', [
            'product' => $product->id,
            'price' => $productPrice->id,
        ]);

        return $productPrice
            ->toResource()
            ->additional(['links' => ['related' => $newResourceLink]])
            ->response()
            ->header('Location', $newResourceLink)
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, ProductPrice $price)
    {
        return $this->productPriceService->show($price)->toResource();
    }
}
