<?php

namespace App\Services;

use App\Actions\Category\FirstOrCreateCategoryAction;
use App\Actions\Category\UpdateCategoryAction;
use App\Models\Category;
use App\Traits\AssertionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    use AssertionTrait;

    private $fields = ['name', 'description'];

    /**
     * Create a new class instance.
     */
    public function __construct(
        private FirstOrCreateCategoryAction $firstOrCreateCategoryAction,
        private UpdateCategoryAction $updateCategoryAction,
    ) {}

    public function all(?int $perPage = 20): LengthAwarePaginator
    {
        return Category::query()->paginate($perPage, ['*'], 'page');
    }

    public function create(array $data): Category
    {
        return $this->firstOrCreateCategoryAction->handle($data);
    }

    public function update(array $data, Category $category): Category
    {
        $this->updateCategoryAction->handle($category, $data);

        return $category->refresh();
    }

    /**
     * TODO - Deprecate
     * Return the existing record or create a new one
     */
    public function firstOrCreate(array $data): Category
    {
        $this->assertShouldHaveKeys($this->fields, $data);

        $user = Auth::guard('web')->user();

        return $user
            ->categories()
            ->firstOrCreate(['name' => $data['name']], ['description' => $data['description'] ?? null]);
    }
}
