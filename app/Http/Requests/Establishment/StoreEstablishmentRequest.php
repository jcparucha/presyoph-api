<?php

namespace App\Http\Requests\Establishment;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreEstablishmentRequest extends EstablishmentRequest
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
