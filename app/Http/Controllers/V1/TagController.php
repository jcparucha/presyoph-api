<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Product;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagController extends Controller
{
    public function __construct(private TagService $tagService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Product $product): JsonResource
    {
        return TagResource::collection($this->tagService->all($product));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     */
    // public function show(Tag $tag)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Product $product)
    {
        return TagResource::collection(
            $this->tagService->update($request->validated(), $product),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Tag $tag)
    // {
    //     //
    // }
}
