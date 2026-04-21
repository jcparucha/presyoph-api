<?php

namespace App\Services;

use App\AssertionTrait;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryHandlerService
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create the existing record or create a new one
     *
     * @param array $data
     * @return Category
     */
    public function firstOrCreate(array $data): Category
    {
        $this->assertRequiredKeys(['name', 'description'], $data);

        return Category::firstOrCreate(
            ['name' => $data['name']],
            [
                'description' => $data['description'],
                'added_by' => Auth::guard('web')->user()->id,
            ],
        );
    }
}
