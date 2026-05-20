<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreTypeResource;
use App\Models\StoreType;
use App\Services\StoreTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreTypeController extends Controller
{
    public function __construct(private StoreTypeService $storeTypeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        return StoreTypeResource::collection($this->storeTypeService->all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StoreType $storeType): JsonResource
    {
        return $this->storeTypeService->get($storeType)->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StoreType $storeType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreType $storeType)
    {
        //
    }
}
