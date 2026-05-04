<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstablishmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // TODO add mun/city, province, and region
        return [
            'id' => $this->id,
            'name' => $this->name,
            'barangay' => $this->barangay->toResource(),
            'store_type' => $this->storeType->toResource(),
        ];
    }
}
