<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
    public function coreRules(): array
    {
        return [
            'username' => ['required', 'alpha_num:ascii', 'min:5', 'max:16'],
            'password' => ['required'],
        ];
    }
}
