<?php

namespace App\Http\Requests\GroceryList;

use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GroceryListRequest extends FormRequest
{
    use HasTextField;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function coreRules(bool $isRequired = true): array
    {
        return [
            'name' => $this->nameRule(type: 'extended', isRequired: $isRequired),
            'description' => $this->descriptionRule(type: 'descriptive', isRequired: $isRequired),
        ];
    }
}
