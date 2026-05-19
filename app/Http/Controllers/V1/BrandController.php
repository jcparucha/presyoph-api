<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandController extends Controller
{
    public function __construct(private BrandService $brandService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(PaginationRequest $request): JsonResource
    {
        return BrandResource::collection(
            $this->brandService->all($request->per_page),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $newBrand = $this->brandService->create($request->validated());

        $newResourceLink = route('brand.show', [
            'brand' => $newBrand->id,
        ]);

        return $newBrand
            ->toResource()
            ->additional(['links' => ['related' => $newResourceLink]])
            ->response()
            ->header('Location', $newResourceLink)
            ->setStatusCode($newBrand->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand): JsonResource
    {
        return $this->brandService->show($brand)->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateBrandRequest $request,
        Brand $brand,
    ): JsonResource {
        return $this->brandService
            ->update($request->validated(), $brand)
            ->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Brand $brand)
    // {
    //     //
    // }
}
