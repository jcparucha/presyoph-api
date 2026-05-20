<?php

namespace App\Http\Requests\Brand;

use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    use HasTextField;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                ...$this->nameRule(),
                Rule::unique('brands', 'name')->ignore($this->brand->id),
            ],
        ];
    }
}
