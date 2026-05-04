<?php

namespace App\Services;

use App\Models\Product;
use App\Traits\AssertionTrait;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProductPriceService
{
    use AssertionTrait;

    private $eagerLoad = [
        'user',
        'establishment',
        'establishment.barangay',
        'establishment.storeType',
    ];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function all(array $inputs, Product $product): LengthAwarePaginator
    {
        $product->load('prices');

        $perPage = isset($inputs['per_page']) ? $inputs['per_page'] : 20;

        $id = isset($inputs['establishment_id'])
            ? $inputs['establishment_id']
            : null;

        return $product
            ->prices()
            ->with($this->eagerLoad)
            ->when($id, function (Builder $query) use ($id) {
                $query->establishment($id);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page');
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
                'product_id' => $data['product_id'],
                'establishment_id' => $data['establishment_id'],
            ],
            [
                'price' => $data['price'],
                'added_by' => Auth::guard('web')->user()->id,
            ],
        );
    }
}
