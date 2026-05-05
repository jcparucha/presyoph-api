<?php

namespace App\Http\Requests\ProductPrice;

use App\Traits\Validations\HasEstablishment;
use App\Traits\Validations\HasPagination;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest
{
    use HasPagination, HasEstablishment;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->paginationRules(),
            'establishment_id' => $this->establishmentRule(true),
        ];
    }
}
