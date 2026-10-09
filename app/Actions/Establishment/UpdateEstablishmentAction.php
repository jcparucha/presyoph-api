<?php

namespace App\Actions\Establishment;

use App\Models\Establishment;
use App\Repositories\EstablishmentRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class UpdateEstablishmentAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(private EstablishmentRepository $repo) {}

    public function handle(Establishment $establishment, array $data): ?Establishment
    {
        try {
            return $this->repo
                ->update($establishment, $data)
                ->load(['storeType', 'barangay.munCity.province.region'])
                ->refresh();
        } catch (QueryException $e) {
            Log::error(__CLASS__.': Database error - '.$e->getMessage());

            throw new \RuntimeException('Something went wrong on updating the establishment.', 0, $e);
        }
    }
}
