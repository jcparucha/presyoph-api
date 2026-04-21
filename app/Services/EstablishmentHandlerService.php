<?php

namespace App\Services;

use App\Models\Establishment;
use Exception;
use Illuminate\Support\Facades\Auth;

class EstablishmentHandlerService
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
     * @return Establishment
     */
    public function firstOrCreate(array $data): Establishment
    {
        if (
            count(
                array_diff(
                    ['name', 'barangay_code', 'store_type_id'],
                    array_keys($data),
                ),
            )
        ) {
            throw new Exception(
                "The fields 'name', 'barangay_code', 'store_type_id' are missing.",
                1, // NOTE: search next time what's the proper code for this.
            );
        }

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
