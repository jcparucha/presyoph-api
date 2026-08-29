<?php

namespace App\Services;

use App\Actions\Brand\FirstOrCreateBrandAction;
use App\Actions\Brand\UpdateBrandNameAction;
use App\Models\Brand;
use App\Repositories\UserRepository;
use App\Traits\AssertionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class BrandService
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepository $userRepo,
        private UpdateBrandNameAction $updateBrandNameAction,
        private FirstOrCreateBrandAction $firstOrCreateBrandAction,
    ) {}

    public function all(?int $perPage = 20): LengthAwarePaginator
    {
        return Brand::paginate($perPage, ['*'], 'page', null, null);
    }

    public function create(array $data): Brand
    {
        $this->assertShouldHaveKeys(['name'], $data);

        return $this->firstOrCreateBrandAction->handle($data['name']);
    }

    public function update(Brand $brand, array $inputs): Brand
    {
        $this->updateBrandNameAction->handle($brand, $inputs['name']);

        return $brand->refresh();
    }

    /**
     * TODO - Deprecate
     * Return the existing record or create a new one
     */
    public function firstOrCreate(array $data): Brand
    {
        $this->assertShouldHaveKeys(['name'], $data);

        $user = Auth::guard('web')->user();

        return $user->brands()->firstOrCreate(['name' => $data['name']]);
    }
}
