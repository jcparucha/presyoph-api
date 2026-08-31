<?php

namespace App\Actions\Brand;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use App\Repositories\UserRepository;
use App\Traits\AssertionTrait;
use Illuminate\Support\Facades\Log;

class FirstOrCreateBrandAction
{
    use AssertionTrait;

    /**
     * Create a new class instance.
     */
    public function __construct(private BrandRepository $brandRepo, private UserRepository $userRepo) {}

    public function handle(array $data): ?Brand
    {
        try {
            $this->assertShouldHaveKeys(['name'], $data);

            return $this->brandRepo->firstOrCreate($this->userRepo->getAuthUser(), $data['name']);
        } catch (\Exception $e) {
            Log::error(__CLASS__.': '.$e->getMessage());

            // TODO: Should throw exception error
            return null;
        }
    }
}
