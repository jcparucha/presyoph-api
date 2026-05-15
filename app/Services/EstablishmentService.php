<?php

namespace App\Services;

use App\Traits\AssertionTrait;
use App\Models\Establishment;
use App\Models\StoreType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class EstablishmentService
{
    use AssertionTrait;

    private $fields = ['name', 'barangay_code', 'store_type'];

    private $eagerLoad = ['storeType', 'barangay.munCity.province.region'];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function all(array $inputs): LengthAwarePaginator
    {
        $perPage = $inputs['per_page'] ?? 20;

        $storeTypeId = $inputs['store_type']
            ? $this->getStoreType($inputs['store_type'])->id
            : false;

        $barangayCode = $inputs['barangay_code'] ?? false;
        $munCityCode = $inputs['mun_city_code'] ?? false;
        $provinceCode = $inputs['province_code'] ?? false;
        $regionCode = $inputs['region_code'] ?? false;

        $establishments = Establishment::with($this->eagerLoad);

        if ($storeTypeId) {
            $establishments->OfStoreType($storeTypeId);
        }

        if ($barangayCode) {
            $establishments->inBarangay($barangayCode);
        } elseif ($munCityCode) {
            $establishments->inMunCity($munCityCode);
        } elseif ($provinceCode) {
            $establishments->inProvince($provinceCode);
        } elseif ($regionCode) {
            $establishments->inRegion($regionCode);
        }

        return $establishments->paginate($perPage, ['*'], 'page');
    }

    public function show(Establishment $establishment): Establishment
    {
        // eager load connections
        return $establishment->load($this->eagerLoad);
    }

    public function create(array $data): Establishment
    {
        return $this->firstOrCreate($data)->load($this->eagerLoad);
    }

    public function update(
        array $inputs,
        Establishment $establishment,
    ): Establishment {
        foreach ($this->fields as $field) {
            if (
                isset($inputs[$field]) &&
                $establishment->$field !== $inputs[$field]
            ) {
                $establishment->$field = $inputs[$field];
            }
        }

        if ($establishment->isDirty()) {
            $establishment->save();
        }

        return $establishment->load($this->eagerLoad)->refresh();
    }

    /**
     * Create the existing record or create a new one
     *
     * @param array $data
     * @return Establishment
     */
    public function firstOrCreate(array $data): Establishment
    {
        $this->assertShouldHaveKeys($this->fields, $data);

        $storeType = $this->getStoreType($data['store_type']);

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

    private function getStoreType(string $storeType): StoreType
    {
        return StoreType::ofType($storeType)->first();
    }
}
