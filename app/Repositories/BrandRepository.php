<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Models\User;
use App\Traits\AssertionTrait;

class BrandRepository
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function firstOrCreate(User $user, string $name): Brand
    {
        return $user->brands()->firstOrCreate(['name' => $name]);
    }

    public function update(Brand $brand, string $name): Brand
    {
        $brand->name = $name;
        $brand->slug = generate_unique_slug($name);
        $brand->save();

        return $brand;
    }
}
