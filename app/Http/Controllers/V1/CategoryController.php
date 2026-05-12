<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\NewCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(PaginationRequest $request): JsonResource
    {
        return CategoryResource::collection(
            $this->categoryService->all($request->per_page),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewCategoryRequest $request)
    {
        $newCategory = $this->categoryService->create($request->validated());

        $newResourceLink = route('category.show', [
            'category' => $newCategory->id,
        ]);

        return $newCategory
            ->toResource()
            ->additional(['links' => ['related' => $newResourceLink]])
            ->response()
            ->header('Location', $newResourceLink)
            ->setStatusCode($newCategory->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResource
    {
        return $this->categoryService->show($category)->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        return $this->categoryService
            ->update($request->validated(), $category)
            ->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
