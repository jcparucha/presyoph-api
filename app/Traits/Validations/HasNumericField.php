<?php

namespace App\Traits\Validations;

trait HasNumericField
{
    public function priceRule(): array
    {
        return ['required', 'numeric', 'min:0.01', 'max:999999.99'];
    }

    public function netWeightRule(
        int $min = 1,
        int $max = 10000,
        bool $isRequired = true,
    ): array {
        return [
            $isRequired ? 'required' : 'sometimes',
            'integer:strict',
            'numeric',
            'min:' . $min,
            'max:' . $max,
        ];
    }
}
