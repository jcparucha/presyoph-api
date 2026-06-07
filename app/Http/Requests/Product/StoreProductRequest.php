<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreProductRequest extends ProductRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [...$this->coreRules(), ...$this->extraRules()];
    }
}
