<?php

namespace App\Http\Requests\Establishment;

use App\Traits\Validations\HasExistsField;
use App\Traits\Validations\HasPagination;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest
{
    use HasPagination, HasExistsField;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->paginationRules(),
            'store_type' => $this->existsRule(
                table: 'store_types',
                column: 'name',
                isRequired: false,
            ),
            'barangay_code' => [
                ...$this->existsRule(
                    table: 'barangays',
                    column: 'code',
                    isRequired: false,
                ),
                'prohibits:mun_city_code,province_code,region_code',
            ],
            'mun_city_code' => [
                ...$this->existsRule(
                    table: 'mun_cities',
                    column: 'code',
                    isRequired: false,
                ),
                'prohibits:barangay_code,province_code,region_code',
            ],
            'province_code' => [
                ...$this->existsRule(
                    table: 'provinces',
                    column: 'code',
                    isRequired: false,
                ),
                'prohibits:barangay_code,mun_city_code,region_code',
            ],
            'region_code' => [
                ...$this->existsRule(
                    table: 'regions',
                    column: 'code',
                    isRequired: false,
                ),
                'prohibits:barangay_code,mun_city_code,province_code',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'prohibits' => 'Only one geolocation filter is allowed.',
        ];
    }
}
