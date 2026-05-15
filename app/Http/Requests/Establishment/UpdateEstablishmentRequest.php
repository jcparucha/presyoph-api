<?php

namespace App\Http\Requests\Establishment;

use App\Traits\Validations\HasExistsField;
use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEstablishmentRequest extends FormRequest
{
    use HasExistsField, HasTextField;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => $this->nameRule(isRequired: false),
            'barangay_code' => $this->existsRule(
                table: 'barangays',
                column: 'code',
                isRequired: false,
            ),
            'store_type' => $this->existsRule(
                table: 'store_types',
                column: 'name',
                isRequired: false,
            ),
        ];
    }
}
