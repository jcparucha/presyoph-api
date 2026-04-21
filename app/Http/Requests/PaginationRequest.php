<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaginationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // TODO: remove per_page: 5 <- this is a test only
        return [
            'page' => ['integer', 'numeric', 'min:1'],
            'per_page' => ['integer', 'numeric', Rule::in([5, 10, 20, 30])],
        ];
    }
}
