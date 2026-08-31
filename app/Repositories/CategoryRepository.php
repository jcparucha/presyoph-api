<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class CategoryRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function firstOrCreate(User $user, array $data): ?Category
    {
        return $user
            ->categories()
            ->firstOrCreate(['name' => $data['name']], ['description' => $data['description'] ?? null]);
    }

    public function update(Category $category, array $data): Category
    {
        $category->fill($data);

        // if changes on name has changed, update the slug
        // TODO - move to observer for Updating and Creating
        if ($category->isDirty('name') && Str::lower($category->name) !== Str::lower($data['name'])) {
            $category->slug = generate_unique_slug($data['name']);
        }

        // `save()` already handles the dirty check
        $category->save();

        return $category;
    }
}
