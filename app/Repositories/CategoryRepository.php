<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function firstOrCreate(User $user, array $data): ?Category
    {
        try {
            return $user
                ->categories()
                ->firstOrCreate(['name' => $data['name']], ['description' => $data['description'] ?? null]);
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ': ' . $e->getMessage());

            // TODO: Should throw exception error
            return null;
        }
    }

    public function update(Category $category, array $data): Category
    {
        foreach (['name', 'description'] as $field) {
            if (isset($data[$field]) && $data[$field] !== $category->$field) {
                // update slug first if name was changed.
                // TODO - moved to observer for Updating and Creating
                if ($field === 'name' && Str::lower($category->$field) !== Str::lower($data[$field])) {
                    $category->slug = generate_unique_slug($data[$field]);
                }

                $category->$field = $data[$field];
            }
        }

        if ($category->isDirty()) {
            $category->save();
        }

        return $category;
    }
}
