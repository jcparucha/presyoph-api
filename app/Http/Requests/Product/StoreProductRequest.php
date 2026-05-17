<?php

namespace App\Http\Requests\Product;

use App\Traits\Validations\HasArrayField;
use App\Traits\Validations\HasExistsField;
use App\Traits\Validations\HasNumericField;
use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    use HasArrayField, HasExistsField, HasNumericField, HasTextField;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => $this->nameRule(),
            'weight' => $this->weightRule(),
            'unit' => $this->existsRule(table: 'units', column: 'abbreviation'),
            'brand' => $this->nameRule(),
            'price' => $this->priceRule(),
            'category.name' => $this->nameRule(alphaRule: 'AlphaSpace'),
            'category.description' => $this->descriptionRule(
                isRequired: false,
                isNullable: true,
            ),
            'tags' => $this->arrayRule(isRequired: false),
            'tags.*' => $this->textItemRule(max: 25, alphaRule: 'AlphaSpace'),
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
