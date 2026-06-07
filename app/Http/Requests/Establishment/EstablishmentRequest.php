<?php

namespace App\Http\Requests\Establishment;

use App\Traits\Validations\HasExistsField;
use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class EstablishmentRequest extends FormRequest
{
    use HasExistsField, HasTextField;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // See Requests/Brand/BrandRequest for the explanation
        if ($this->has('name')) {
            $this->merge(['name' => Str::squish($this->name)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function coreRules(bool $isRequired = true): array
    {
        return [
            'name' => $this->nameRule(isRequired: $isRequired),
            'barangay_code' => $this->existsRule(table: 'barangays', column: 'code', isRequired: $isRequired),
            'store_type' => $this->existsRule(table: 'store_types', column: 'name', isRequired: $isRequired),
        ];
    }
}
