<?php

namespace App\Http\Requests\GroceryList;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexGroceryListRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // $this->published = $this->published === 'true' ? true : false;
        if ($this->has('published')) {
            $this->merge([
                'published' => in_array($this->published, ['TRUE', 'true', '1', 1])
                    ? true
                    : (in_array($this->published, ['FALSE', 'false', '0', 0])
                        ? false
                        : $this->published),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'published' => ['sometimes', 'boolean:strict'],
        ];
    }
}
