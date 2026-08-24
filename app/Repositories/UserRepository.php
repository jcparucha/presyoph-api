<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    /**
     * @param  array  $credentials  = [username, password]
     */
    public function register(array $credentials): User
    {
        return User::realUser()->create([
            'username' => $credentials['username'],
            'password' => Hash::make($credentials['password']),
        ]);
    }

    public function getByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    public function setDefaultEntitlements(User $user): void
    {
        // set default max Grocery List of 3
        $user->defaultMaxGroceryLists()->create();
    }
}
