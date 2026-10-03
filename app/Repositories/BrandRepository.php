<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Models\User;
use App\Traits\AssertionTrait;
use Illuminate\Support\Str;

class BrandRepository
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function firstOrCreate(User $user, string $name): Brand
    {
        return Brand::query()->firstOrCreate(['name' => $name], ['added_by' => $user->id]);
    }

    public function update(Brand $brand, string $name): Brand
    {
        $brand->name = $name;

        // if name has changes, update the slug
        // TODO - move to observer for Updating and Creating
        if ($brand->isDirty('name') && Str::lower($brand->name) !== Str::lower('name')) {
            $brand->slug = generate_unique_slug($name);
        }

        // `save()` already handles the dirty check
        $brand->save();

        return $brand;
    }
}
