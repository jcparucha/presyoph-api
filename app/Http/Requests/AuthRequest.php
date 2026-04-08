<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // allow users who are not logged in
        return is_null($this->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        $usernameRule = ['required', 'alpha_num:ascii', 'min:5', 'max:16'];

        $passwordRule = ['required'];

        if ($request->routeIs('register')) {
            // add validation for unique username
            array_push($usernameRule, 'unique:users');

            /**
             * add validation for
             * - password and password_confirmation should be the same
             * - password should be min of 8, max of 16, should lower & upper case, and with number
             */
            array_push(
                $passwordRule,
                'confirmed',
                Password::min(8)->max(16)->mixedCase()->numbers(),
            );
        }

        return [
            'username' => $usernameRule,
            'password' => $passwordRule,
        ];
    }
}
