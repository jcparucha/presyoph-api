<?php

namespace App\Http\Requests\Establishment;

use App\Models\Barangay;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class UpdateEstablishmentRequest extends EstablishmentRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = $this->coreRules(isRequired: false);

        $name = $this->getName();
        $barangayCode = $this->getBarangayCode();

        return [
            'name' => [
                ...$rules['name'],
                Rule::unique('establishments')
                    ->where(fn (Builder $query) => $query->where('barangay_code', $barangayCode))
                    ->ignore($this->establishment->id),
            ],
            'barangay_code' => [
                ...$rules['barangay_code'],
                Rule::unique('establishments')
                    ->where(fn (Builder $query) => $query->where('name', $name))
                    ->ignore($this->establishment->id),
            ],
            'store_type' => $rules['store_type'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $name = $this->getName();
        $barangay = Barangay::where('code', $this->getBarangayCode())->first()->name;

        return [
            'name.unique' => "This establishment already exists in $barangay.",
            'barangay_code.unique' => "This location already has $name.",
        ];
    }

    private function getName(): string
    {
        return $this->name ?? $this->establishment->name;
    }

    private function getBarangayCode(): string
    {
        return $this->barangay_code ?? $this->establishment->barangay_code;
    }
}
