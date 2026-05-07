<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'recorded_at' => $this->created_at,
            'added_by' => $this->user?->username,
            'establishment' => new EstablishmentResource(
                $this->whenLoaded('establishment'),
            ),
        ];
    }
}
