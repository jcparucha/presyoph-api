<?php

namespace App\Traits\Validations;

trait HasPrice
{
    // TODO add this also in the Product
    public function priceRule(): array
    {
        return ['required', 'numeric', 'min:0.01', 'max:999999.99'];
    }
}
