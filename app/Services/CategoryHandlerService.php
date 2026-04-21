<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Auth;

class CategoryHandlerService
{
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
        if (count(array_diff(['name', 'description'], array_keys($data)))) {
            throw new Exception(
                "The fields 'name' and 'description' are missing.",
                1, // NOTE: search next time what's the proper code for this.
            );
        }

        return Category::firstOrCreate(
            ['name' => $data['name']],
            [
                'description' => $data['description'],
                'added_by' => Auth::guard('web')->user()->id,
            ],
        );
    }
}
