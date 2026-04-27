<?php

namespace App\Services;

use App\Traits\AssertionTrait;
use App\Models\Brand;
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

    /**
     * Create the existing record or create a new one
     *
     * @param array $data
     * @return Brand
     */
    public function firstOrCreate(array $data): Brand
    {
        $this->assertShouldHaveKeys(['name'], $data);

        return Brand::firstOrCreate(
            ['name' => $data['name']],
            ['added_by' => Auth::guard('web')->user()->id],
        );
    }
}
