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
        // TODO improve pa
        return [
            'id' => $this->id,
            'name' => $this->name,
            // `barangay` => $this->barangay,
            // `story_type` => $this->story_type,
            // `created_at` => $this->created_at,
        ];
    }
}
