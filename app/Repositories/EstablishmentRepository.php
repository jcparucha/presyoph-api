<?php

namespace App\Repositories;

use App\Models\Establishment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class EstablishmentRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function firstOrCreate(User $user, array $data): Establishment
    {
        return $user->establishments()->firstOrCreate(
            [
                'name' => $data['name'],
                'barangay_code' => $data['barangay_code'],
            ],
            [
                'store_type_id' => $data['store_type_id'],
            ],
        );
    }

    public function update(Establishment $establishment, array $data): Establishment
    {
        $establishment->fill($data);
        $establishment->save();

        return $establishment;
    }

    public function getAll(array $data): Builder
    {
        return Establishment::query()
            ->with(['storeType', 'barangay.munCity.province.region'])
            ->when($data['store_type_id'], fn ($query, $value) => $query->ofStoreType($value))
            ->when($data['barangay_code'], fn ($query, $value) => $query->ofStoreType($value))
            ->when($data['mun_city_code'], fn ($query, $value) => $query->ofStoreType($value))
            ->when($data['province_code'], fn ($query, $value) => $query->ofStoreType($value))
            ->when($data['region_code'], fn ($query, $value) => $query->ofStoreType($value));
    }
}
