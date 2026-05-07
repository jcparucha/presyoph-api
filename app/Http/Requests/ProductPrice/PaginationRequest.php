<?php

namespace App\Http\Requests\ProductPrice;

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
            'establishment_id' => $this->existsRule(
                table: 'establishments',
                isRequired: false,
            ),
        ];
    }
}
