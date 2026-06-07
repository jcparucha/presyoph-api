<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreCategoryRequest extends CategoryRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->coreRules();
    }
}
