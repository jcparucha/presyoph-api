<?php

namespace App\Http\Requests\Product;

use App\Traits\Validations\HasExistsField;
use App\Traits\Validations\HasNumericField;
use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewProductRequest extends FormRequest
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
            'name' => $this->nameRule(),
            'net_weight' => $this->netWeightRule(),
            'unit' => $this->existsRule(table: 'units', column: 'abbreviation'),
            'brand' => $this->nameRule(),
            'price' => $this->priceRule(),
            'category.name' => $this->nameRule(alphaRule: 'AlphaSpace'),
            'category.description' => $this->descriptionRule(
                isRequired: false,
                isNullable: true,
            ),
            'tags' => ['sometimes', 'array'],
            'tags.*' => $this->nameRule(max: 25, isRequired: false),
            'establishment.name' => $this->nameRule(),
            'establishment.barangay_code' => $this->existsRule(
                table: 'barangays',
                column: 'code',
            ),
            'establishment.store_type' => $this->existsRule(
                table: 'store_types',
                column: 'name',
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
