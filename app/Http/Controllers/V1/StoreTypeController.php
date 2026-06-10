<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreTypeResource;
use App\Services\StoreTypeService;
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
}
