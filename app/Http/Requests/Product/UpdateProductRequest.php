<?php

namespace App\Http\Requests\Product;

use App\Rules\AlphaCharNumSpace;
use App\Rules\AlphaSpace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'min:3',
                'max:50',
                new AlphaCharNumSpace(),
            ],
            'net_weight' => [
                'sometimes',
                'required',
                'integer:strict',
                'numeric',
                'min:1',
                'max:10000',
            ],
            'unit' => ['sometimes', 'required', 'exists:units,abbreviation'],
            'brand' => [
                'sometimes',
                'required',
                'min:3',
                'max:50',
                new AlphaCharNumSpace(),
            ],
            'category.name' => [
                'sometimes',
                'required',
                'min:3',
                'max:50',
                new AlphaSpace(),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }
}
