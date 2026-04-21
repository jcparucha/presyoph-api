<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'net_weight' => $this->net_weight,
            'unit' => $this->unit->toResource(),
            'brand' => $this->brand->toResource(),
            'category' => $this->category->toResource(),
            'added_by' => $this->user->toResource(),
            'tags' => TagResource::collection($this->tags),
            'prices' => ProductPriceResource::collection($this->prices),
        ];
    }
}
