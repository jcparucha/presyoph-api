<?php

namespace App\Services;

use App\Actions\Establishment\FirstOrCreateEstablishmentAction;
use App\Actions\Establishment\UpdateEstablishmentAction;
use App\Models\Establishment;
use App\Models\StoreType;
use App\Repositories\EstablishmentRepository;
use App\Traits\AssertionTrait;
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
    public function __construct(
        private EstablishmentRepository $repo,
        private FirstOrCreateEstablishmentAction $firstOrCreateAction,
        private UpdateEstablishmentAction $updateAction,
    ) {}

    public function all(array $inputs): LengthAwarePaginator
    {
        // returns StoreType->id, else null
        $inputs['store_type_id'] = optional($this->getStoreType($inputs['store_type']))->id;

        // TODO - apply this for pagination: https://laravel.com/framework/docs/queries#query-pipes
        return $this->repo->getAll($inputs)->paginate($inputs['per_page'] ?? 20, ['*'], 'page');
    }

    public function show(Establishment $establishment): Establishment
    {
        // eager load connections
        return $establishment->load($this->eagerLoad);
    }

    public function create(array $data): Establishment
    {
        return $this->firstOrCreateAction
            ->handle([...$data, 'store_type_id' => $this->getStoreType($data['store_type'])->id])
            ->load($this->eagerLoad);
    }

    public function update(array $inputs, Establishment $establishment): Establishment
    {
        // if has store_type in the $inputs, get the StoreType->id for mass assignable, else do nothing.
        if (! empty($inputs['store_type'])) {
            $inputs['store_type_id'] = $this->getStoreType($inputs['store_type'])->id;
        }

        return $this->updateAction->handle($establishment, $inputs)->load($this->eagerLoad)->refresh();
    }

    /**
     * TODO - Deprecate
     * Return the existing record or create a new one
     */
    public function firstOrCreate(array $data): Establishment
    {
        $this->assertShouldHaveKeys($this->fields, $data);

        $user = Auth::guard('web')->user();

        $storeType = $this->getStoreType($data['store_type']);

        return $user->establishments()->firstOrCreate(
            [
                'name' => $data['name'],
                'barangay_code' => $data['barangay_code'],
            ],
            [
                'store_type_id' => $storeType->id,
            ],
        );
    }

    private function getStoreType(string $storeType): StoreType
    {
        return StoreType::ofType($storeType)->first();
    }
}
