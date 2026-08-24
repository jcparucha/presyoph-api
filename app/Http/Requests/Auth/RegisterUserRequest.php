<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends AuthRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        ['username' => $usernameRule, 'password' => $passwordRule] = $this->coreRules();

        return [
            // validation for unique username
            'username' => [...$usernameRule, 'unique:users'],
            // password should be min of 8, max of 16, should lower & upper case, and with number
            'password' => [...$passwordRule, 'confirmed', Password::min(8)->max(16)->mixedCase()->numbers()],
        ];
    }
}
