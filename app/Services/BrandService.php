<?php

namespace App\Services;

use App\Models\Brand;
use App\Traits\AssertionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class BrandService
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function all(?int $perPage = 20): LengthAwarePaginator
    {
        return Brand::paginate($perPage, ['*'], 'page');
    }

    public function show(Brand $brand): Brand
    {
        return $brand;
    }

    public function create(array $data): Brand
    {
        return $this->firstOrCreate($data);
    }

    public function update(array $inputs, Brand $brand): Brand
    {
        $brand->name = $inputs['name'];
        $brand->slug = generate_unique_slug($inputs['name']);
        $brand->save();

        return $brand->refresh();
    }

    /**
     * Return the existing record or create a new one
     */
    public function firstOrCreate(array $data): Brand
    {
        $this->assertShouldHaveKeys(['name'], $data);

        return Brand::firstOrCreate(
            ['name' => $data['name']],
            [
                'slug' => generate_unique_slug($data['name']),
                'added_by' => Auth::guard('web')->user()->id,
            ],
        );
    }
}
