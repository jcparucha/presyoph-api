<?php

namespace App\Contracts;

interface AuthServiceInterface
{
    /**
     * Register the user
     *
     * @return bool TRUE - Registration Successful
     */
    public function register(array $data): bool;

    /**
     * Login the user
     *
     * @param  array  $credentials[username,  password]
     * @return string CredentialStatus 'VALID', 'INVALID', 'NON_EXISTENT'
     */
    public function login(array $credentials): string;

    /**
     * Logout the user
     */
    public function logout(): void;
}
