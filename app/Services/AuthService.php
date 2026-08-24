<?php

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Enums\CredentialStatus;
use App\Facades\Actions\RegisterUser;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class AuthService implements AuthServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UserRepository $userRepo) {}

    public function register(array $data): bool
    {
        return ! is_null(RegisterUser::handle($data));
    }

    public function login(array $credentials): string
    {
        if (Auth::guard('web')->attempt($credentials)) {
            session()->regenerate();

            return CredentialStatus::VALID->name;
        }

        return ! is_null($this->userRepo->getByUsername($credentials['username']))
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
