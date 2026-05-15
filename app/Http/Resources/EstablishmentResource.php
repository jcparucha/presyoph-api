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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'store_type' => new StoreTypeResource(
                $this->whenLoaded('storeType'),
            ),
            'address' => $this->whenLoaded('barangay', function () {
                $barangay = $this->barangay;
                $munCity = $barangay?->munCity;
                $province = $munCity?->province;
                $region = $province?->region;

                return [
                    'barangay' => $barangay?->name,
                    'munCity' => $munCity?->name,
                    'province' => $province?->name,
                    'region' => $region?->name,
                ];
            }),
        ];
    }
}
