<?php

namespace App\Http\Requests\Tag;

use App\Traits\Validations\HasArrayField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class TagRequest extends FormRequest
{
    use HasArrayField;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // See Requests/Brand/BrandRequest for the explanation
        if ($this->has('tags')) {
            $tags = array_map(fn ($value) => Str::squish($value), array_filter($this->tags));
            $this->merge(['tags' => $tags]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function coreRules(): array
    {
        return [
            'tags' => $this->arrayRule(isRequired: false),
            'tags.*' => $this->textItemRule(),
        ];
    }
}
