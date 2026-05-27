<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroceryListResource;
use App\Models\User;
use App\Services\GroceryListService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroceryListController extends Controller
{
    public function __construct(private GroceryListService $groceryListService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(User $user): JsonResource
    {
        return GroceryListResource::collection($this->groceryListService->all($user));
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
