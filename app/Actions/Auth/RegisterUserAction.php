<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterUserAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UserRepository $userRepo) {}

    /**
     * @param  array  $credentials  [username, password]
     */
    public function handle(array $credentials): ?User
    {
        try {
            return DB::transaction(function () use ($credentials) {
                // register the user first
                $newUser = $this->userRepo->register($credentials);
                // then set entitlements for new user
                $this->userRepo->setDefaultEntitlements($newUser);

                return $newUser;
            });
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ': ' . $e->getMessage());

            // TODO: Should throw exception error
            return null;
        }
    }
}
