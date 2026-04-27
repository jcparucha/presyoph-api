<?php

namespace App\Http\Requests\Product;

use App\Rules\AlphaCharNumSpace;
use App\Rules\AlphaSpace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:50', new AlphaCharNumSpace()],
            'net_weight' => [
                'required',
                'integer:strict',
                'numeric',
                'min:1',
                'max:10000',
            ],
            'unit' => ['required', 'exists:units,abbreviation'],
            'brand' => ['required', 'min:3', 'max:50', new AlphaCharNumSpace()],
            'price' => ['required', 'decimal:0,2', 'min:1', 'max:1000000'],
            'category.name' => [
                'required',
                'min:3',
                'max:100',
                new AlphaSpace(),
            ],
            'category.description' => [
                'sometimes',
                'nullable',
                'min:3',
                'max:255',
                new AlphaCharNumSpace(),
            ],
            'tags' => ['sometimes', 'array'],
            'tags.*' => [
                'sometimes',
                'min:3',
                'max:25',
                new AlphaCharNumSpace(),
            ],
            'establishment.name' => [
                'required',
                'min:3',
                'max:50',
                new AlphaCharNumSpace(),
            ],
            'establishment.barangay_code' => [
                'required',
                'exists:barangays,code',
            ],
            'establishment.store_type' => [
                'required',
                'exists:store_types,name',
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
