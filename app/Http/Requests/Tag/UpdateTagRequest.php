<?php

namespace App\Http\Requests\Tag;

use App\Traits\Validations\HasArrayField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    use HasArrayField;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tags' => $this->arrayRule(isRequired: false),
            'tags.*' => $this->textItemRule(max: 25, alphaRule: 'AlphaSpace'),
        ];
    }
}
