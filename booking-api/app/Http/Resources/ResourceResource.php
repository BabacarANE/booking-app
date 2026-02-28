<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'price_per_night' => $this->price_per_night,
            'capacity'        => $this->capacity,
            'amenities'       => $this->amenities,
            'images'          => $this->images,
            'location'        => $this->location,
            'is_active'       => $this->is_active,
            'category'        => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
