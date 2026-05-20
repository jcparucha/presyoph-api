<?php

namespace App\Services;

use App\Models\StoreType;
use Illuminate\Support\Collection;

class StoreTypeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function all(): Collection
    {
        return StoreType::all();
    }

    public function get(StoreType $storeType): StoreType
    {
        return $storeType;
    }
}
