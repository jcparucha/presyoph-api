<?php

namespace App\Services;

use App\Models\Brand;
use Exception;
use Illuminate\Support\Facades\Auth;

class BrandHandlerService
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
     * @param string $brand
     * @return Brand
     */
    public function firstOrCreate(string $brand): Brand
    {
        if (!isset($brand) || strlen($brand) === 0) {
            throw new Exception(
                "The fields 'brand' is missing.",
                1, // NOTE: search next time what's the proper code for this.
            );
        }

        return Brand::firstOrCreate(
            ['name' => $brand],
            ['added_by' => Auth::guard('web')->user()->id],
        );
    }
}
