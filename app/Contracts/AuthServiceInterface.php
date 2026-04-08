<?php

namespace App\Contracts;

interface AuthServiceInterface
{
    /**
     * Register the user
     *
     * @param array $data
     * @return boolean TRUE - Registration Successful
     */
    public function register(array $data): bool;

    /**
     * Login the user
     *
     * @param array $credentials[username, password]
     * @return string CredentialStatus 'VALID', 'INVALID', 'NON_EXISTENT'
     */
    public function login(array $credentials): string;

    /**
     * Logout the user
     *
     * @return void
     */
    public function logout(): void;
}
