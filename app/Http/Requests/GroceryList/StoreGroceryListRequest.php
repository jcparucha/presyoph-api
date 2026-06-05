<?php

namespace App\Http\Requests\GroceryList;

use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGroceryListRequest extends FormRequest
{
    use HasTextField;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $specialChars = config('validation.special_chars_sets');

        return [
            'name' => $this->_nameRule(allowCharacters: $specialChars['extended']),
            'description' => $this->_descriptionRule(allowCharacters: $specialChars['descriptive']),
        ];
    }
}
