<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class UpdateCategoryAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(private CategoryRepository $categoryRepo) {}

    public function handle(Category $category, array $data): ?Category
    {
        try {
            return $this->categoryRepo->update($category, $data)->refresh();
        } catch (QueryException $e) {
            Log::error(__CLASS__.': Database error - '.$e->getMessage());

            throw new \RuntimeException('Something went wrong on updating the category.', 0, $e);
        }
    }
}
