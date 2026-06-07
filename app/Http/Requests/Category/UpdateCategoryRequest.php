<?php

namespace App\Http\Requests\Category;

use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends CategoryRequest
{
    use HasTextField;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = $this->coreRules(isRequired: false);

        return [
            'name' => [...$rules['name'], Rule::unique('categories', 'name')->ignore($this->category->id)],
            'description' => $rules['description'],
        ];
    }
}
