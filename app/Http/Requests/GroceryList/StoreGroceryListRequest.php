<?php

namespace App\Http\Requests\GroceryList;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreGroceryListRequest extends GroceryListRequest
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
