<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use App\Traits\AssertionTrait;
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
        } catch (\Exception $e) {
            Log::error(__CLASS__.': '.$e->getMessage());

            // TODO: Should throw exception error
            return null;
        }
    }
}
