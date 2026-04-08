<?php

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Enums\CredentialStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function register(array $data): bool
    {
        $newUser = User::realUser()->create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        return !is_null($newUser);
    }

    public function login(array $credentials): string
    {
        if (Auth::guard('web')->attempt($credentials)) {
            session()->regenerate();

            return CredentialStatus::VALID->name;
        }

        $user = User::where('username', $credentials['username'])->first();

        return !is_null($user)
            ? CredentialStatus::INVALID->name
            : CredentialStatus::NON_EXISTENT->name;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();

        session()->invalidate();

        session()->regenerateToken();
    }
}
