<?php

namespace App\Http\Requests\Category;

use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => $this->nameRule(alphaRule: 'AlphaSpace'),
            'description' => $this->descriptionRule(),
        ];
    }
}
