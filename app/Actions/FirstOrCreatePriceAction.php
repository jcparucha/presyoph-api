<?php

namespace App\Actions;

use App\Models\ProductPrice;
use App\Services\ProductPriceService;
use App\Traits\AssertionTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class FirstOrCreatePriceAction
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct(private ProductPriceService $productPriceService) {}

    public function handle(array $data): ?ProductPrice
    {
        try {
            $this->assertShouldHaveKeys(['product_id', 'establishment_id', 'price'], $data);

            return $this->productPriceService->firstOrCreate([
                'product_id' => $data['product_id'],
                'establishment_id' => $data['establishment_id'],
                'price' => $data['price'],
            ]);
        } catch (QueryException $e) {
            Log::error(__CLASS__.': Database error - '.$e->getMessage());

            throw new \RuntimeException('Something went wrong on saving the product price.', 0, $e);
        }
    }
}
