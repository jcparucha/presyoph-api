<?php

namespace App\Http\Requests\Product;

use App\Traits\Validations\HasExistsField;
use App\Traits\Validations\HasNumericField;
use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    use HasExistsField, HasNumericField, HasTextField;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => $this->nameRule(isRequired: false),
            'weight' => $this->weightRule(isRequired: false),
            'unit' => $this->existsRule(
                table: 'units',
                column: 'abbreviation',
                isRequired: false,
            ),
            'brand' => $this->nameRule(isRequired: false),
            'category.name' => $this->nameRule(
                isRequired: false,
                alphaRule: 'AlphaSpace',
            ),
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
