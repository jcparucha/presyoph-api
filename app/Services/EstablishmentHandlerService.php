<?php

namespace App\Services;

use App\AssertionTrait;
use App\Models\Establishment;
use Illuminate\Support\Facades\Auth;

class EstablishmentHandlerService
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
        $this->assertRequiredKeys(
            ['name', 'barangay_code', 'store_type_id'],
            $data,
        );

        return Establishment::firstOrCreate(
            [
                'name' => $data['name'],
                'barangay_code' => $data['barangay_code'],
            ],
            [
                'store_type_id' => $data['store_type_id'],
                'added_by' => Auth::guard('web')->user()->id,
            ],
        );
    }
}
