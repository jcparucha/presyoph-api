<?php

namespace App\Actions\Establishment;

use App\Models\Establishment;
use App\Repositories\EstablishmentRepository;
use App\Repositories\UserRepository;
use App\Traits\AssertionTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class FirstOrCreateEstablishmentAction
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct(private EstablishmentRepository $repo, private UserRepository $userRepo) {}

    public function handle(array $data): ?Establishment
    {
        try {
            $this->assertShouldHaveKeys(['name', 'barangay_code', 'store_type_id'], $data);

            return $this->repo
                ->firstOrCreate($this->userRepo->getAuthUser(), $data)
                ->load(['storeType', 'barangay.munCity.province.region']);
        } catch (QueryException $e) {
            Log::error(__CLASS__.': Database error - '.$e->getMessage());

            throw new \RuntimeException('Something went wrong on saving the establishment.', 0, $e);
        }
    }
}
