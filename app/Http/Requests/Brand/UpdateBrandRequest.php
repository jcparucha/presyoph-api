<?php

namespace App\Http\Requests\Brand;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends BrandRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = $this->coreRules();

        return [
            'name' => [...$rules['name'], Rule::unique('brands', 'name')->ignore($this->brand->id)],
        ];
    }
}
