<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Repositories\CategoryRepository;
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
            return $this->categoryRepo->update($category, $data);
        } catch (\Exception $e) {
            Log::error(__CLASS__.': '.$e->getMessage());

            // TODO: Should throw exception error
            return null;
        }
    }
}
