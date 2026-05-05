<?php

namespace App\Http\Requests\ProductPrice;

use App\Traits\Validations\HasEstablishment;
use App\Traits\Validations\HasPrice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewProductPriceRequest extends FormRequest
{
    use HasEstablishment, HasPrice;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'price' => $this->priceRule(),
            'establishment_id' => $this->establishmentRule(),
        ];
    }
}
