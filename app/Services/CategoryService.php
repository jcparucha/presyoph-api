<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\AssertionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    use AssertionTrait;

    private $fields = ['name', 'description'];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function all(?int $perPage = 20): LengthAwarePaginator
    {
        return Category::with('user')->paginate($perPage, ['*'], 'page');
    }

    public function show(Category $category): Category
    {
        return $category->load('user');
    }

    public function create(array $data): Category
    {
        return $this->firstOrCreate($data);
    }

    public function update(array $inputs, Category $category): Category
    {
        foreach ($this->fields as $field) {
            if (
                isset($inputs[$field]) &&
                $inputs[$field] !== $category->$field
            ) {
                $category->$field = $inputs[$field];

                // update slug if name was changed
                if ($field === 'name') {
                    $category->slug = generate_unique_slug('name');
                }
            }
        }

        if ($category->isDirty()) {
            $category->save();
        }

        return $category->refresh();
    }

    /**
     * Create the existing record or create a new one
     */
    public function firstOrCreate(array $data): Category
    {
        $this->assertShouldHaveKeys($this->fields, $data);

        return Category::firstOrCreate(
            ['name' => $data['name']],
            [
                'slug' => generate_unique_slug($data['name']),
                'description' => $data['description'] ?? null,
                'added_by' => Auth::guard('web')->user()->id,
            ],
        );
    }
}
