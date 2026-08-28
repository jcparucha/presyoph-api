<?php

namespace App\Actions\Brand;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class FirstOrCreateBrandAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(private BrandRepository $brandRepo, private UserRepository $userRepo) {}

    public function handle(string $name): ?Brand
    {
        try {
            return $this->brandRepo->firstOrCreate($this->userRepo->getAuthUser(), $name);
        } catch (\Exception $e) {
            Log::error(__CLASS__.': '.$e->getMessage());

            // TODO: Should throw exception error
            return null;
        }
    }
}
