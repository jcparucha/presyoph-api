<?php

namespace App\Actions\Brand;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Support\Facades\Log;

class UpdateBrandNameAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(private BrandRepository $brandRepo) {}

    public function handle(Brand $brand, string $name): ?Brand
    {
        try {
            return $this->brandRepo->update($brand, $name);
        } catch (\Exception $e) {
            Log::error(__CLASS__.': '.$e->getMessage());

            // TODO: Should throw exception error
            return null;
        }
    }
}
