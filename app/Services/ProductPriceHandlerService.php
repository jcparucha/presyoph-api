<?php

namespace App\Services;

use App\Traits\AssertionTrait;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Auth;

class ProductPriceHandlerService
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
     * @return ProductPrice
     */
    public function firstOrCreate(array $data): ProductPrice
    {
        $this->assertShouldHaveKeys(
            ['product_id', 'establishment_id', 'price'],
            $data,
        );

        return ProductPrice::firstOrCreate(
            [
                'price' => $data['price'],
                'product_id' => $data['product_id'],
                'establishment_id' => $data['establishment_id'],
            ],
            ['added_by' => Auth::guard('web')->user()->id],
        );
    }
}
