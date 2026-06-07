<?php

namespace App\Http\Requests\Product;

use App\Traits\Validations\HasArrayField;
use App\Traits\Validations\HasExistsField;
use App\Traits\Validations\HasNumericField;
use App\Traits\Validations\HasTextField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ProductRequest extends FormRequest
{
    use HasArrayField, HasExistsField, HasNumericField, HasTextField;

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
        $names = [];
        $plain = ['name', 'brand'];
        $assoc = ['category.name', 'establishment.name'];

        foreach ([...$plain, ...$assoc, 'tags'] as $key) {
            if ($this->has($key)) {
                if (in_array($key, $plain)) {
                    $names[$key] = Str::squish($this->$key);
                }

                if (in_array($key, $assoc)) {
                    [$parent, $name] = explode('.', $key);
                    // keep the other, but overwrite the name
                    $names[$parent] = [...$this->$parent, $name => Str::squish($this->$parent[$name])];
                }

                if ($key === 'tags') {
                    $names['tags'] = array_map(fn ($value) => Str::squish($value), array_filter($this->tags));
                }
            }
        }

        $this->merge(array_filter([...$names]));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function coreRules(bool $isRequired = true): array
    {
        return [
            'name' => $this->nameRule(isRequired: $isRequired),
            'weight' => $this->weightRule(isRequired: $isRequired),
            'unit' => $this->existsRule(table: 'units', column: 'abbreviation', isRequired: $isRequired),
            'brand' => $this->nameRule(isRequired: $isRequired),
            'category.name' => $this->nameRule(isRequired: $isRequired),
        ];
    }

    public function extraRules(): array
    {
        return [
            'price' => $this->priceRule(),
            'category.description' => $this->descriptionRule(isRequired: false, isNullable: true),
            'tags' => $this->arrayRule(isRequired: false),
            'tags.*' => $this->textItemRule(),
            'establishment.name' => $this->nameRule(),
            'establishment.barangay_code' => $this->existsRule(table: 'barangays', column: 'code'),
            'establishment.store_type' => $this->existsRule(table: 'store_types', column: 'name'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'establishment.barangay_code' => 'establishment.barangay_code',
            'establishment.store_type' => 'establishment.store_type',
        ];
    }
}
