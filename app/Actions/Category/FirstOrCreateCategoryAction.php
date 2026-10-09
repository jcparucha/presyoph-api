<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use App\Traits\AssertionTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class FirstOrCreateCategoryAction
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct(private CategoryRepository $categoryRepo, private UserRepository $userRepo) {}

    public function handle(array $data): ?Category
    {
        try {
            $this->assertShouldHaveKeys(['name', 'description'], $data);

            return $this->categoryRepo->firstOrCreate($this->userRepo->getAuthUser(), $data);
        } catch (QueryException $e) {
            Log::error(__CLASS__.': Database error - '.$e->getMessage());

            throw new \RuntimeException('Something went wrong on saving the category.', 0, $e);
        }
    }
}
