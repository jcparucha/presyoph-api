<?php

namespace App\Services;

use App\Models\ProductPrice;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductPriceHandlerService
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
     * @param integer $productID
     * @param integer $establishmentID
     * @param float $price
     * @return ProductPrice
     */
    public function firstOrCreate(
        int $productID,
        int $establishmentID,
        float $price,
    ): ProductPrice {
        if (!isset($productID) || !isset($establishmentID) || !isset($price)) {
            throw new Exception(
                "The fields 'productID', 'establishmentID', and 'price' are missing.",
                1, // NOTE: search next time what's the proper code for this.
            );
        }

        return ProductPrice::firstOrCreate(
            [
                'price' => $price,
                'product_id' => $productID,
                'establishment_id' => $establishmentID,
            ],
            ['added_by' => Auth::guard('web')->user()->id],
        );
    }
}
