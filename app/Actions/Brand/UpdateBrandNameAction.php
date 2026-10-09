<?php

namespace App\Actions\Brand;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Database\QueryException;
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
            return $this->brandRepo->update($brand, $name)->refresh();
        } catch (QueryException $e) {
            Log::error(__CLASS__.': Database error - '.$e->getMessage());

            throw new \RuntimeException('Something went wrong on updating the brand.', 0, $e);
        }
    }
}
