<?php

namespace App\Actions\Establishment;

use App\Models\Establishment;
use App\Repositories\EstablishmentRepository;
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
            return $this->repo->update($establishment, $data);
        } catch (\Exception $e) {
            Log::error(__CLASS__.': '.$e->getMessage());

            // TODO: Should throw exception error
            return null;
        }
    }
}
