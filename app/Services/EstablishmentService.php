<?php

namespace App\Services;

use App\Traits\AssertionTrait;
use App\Models\Establishment;
use App\Models\StoreType;
use Illuminate\Support\Facades\Auth;

class EstablishmentService
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
     * @return Establishment
     */
    public function firstOrCreate(array $data): Establishment
    {
        $this->assertShouldHaveKeys(
            ['name', 'barangay_code', 'store_type'],
            $data,
        );

        $storeType = StoreType::ofType($data['store_type'])->first();

        return Establishment::firstOrCreate(
            [
                'name' => $data['name'],
                'barangay_code' => $data['barangay_code'],
            ],
            [
                'store_type_id' => $storeType->id,
                'added_by' => Auth::guard('web')->user()->id,
            ],
        );
    }
}
