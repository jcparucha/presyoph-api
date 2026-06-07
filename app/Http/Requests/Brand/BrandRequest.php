<?php

namespace App\Http\Requests\Brand;

use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BrandRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Important: "  Colgate  " and "Colgate" are different and will not match on MySQL
        // Using `utf8mb4_unicode_ci` (case-insensitive), "ColGate", "cOlGaTe", "Colgate" are treated as identical.
        // Str::squish() removes trailing, leading, and accidental double spaces inside the name
        // This guarantees database uniqueness via `firstOrCreate` and keeps the POST endpoint strictly idempotent.
        if ($this->has('name')) {
            $this->merge(['name' => Str::squish($this->name)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function coreRules(): array
    {
        return ['name' => [...$this->nameRule()]];
    }
}
