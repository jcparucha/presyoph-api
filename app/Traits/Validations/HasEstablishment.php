<?php

namespace App\Traits\Validations;

trait HasEstablishment
{
    public function establishmentRule(bool $isSometimes = false): array
    {
        return array_filter([
            $isSometimes ? 'sometimes' : '',
            'required',
            'integer',
            'exists:establishments,id',
        ]);
    }
}
