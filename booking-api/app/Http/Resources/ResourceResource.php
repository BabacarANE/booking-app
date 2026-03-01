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
            'location'        => $this->location,
            'is_active'       => $this->is_active,
            'category'        => new CategoryResource($this->whenLoaded('category')),
            // ✅ Nouvelles images
            'images_list'     => $this->whenLoaded('images_list', fn() =>
            $this->images_list->map(fn($img) => [
                'id'         => $img->id,
                'url'        => asset($img->url),
                'is_primary' => $img->is_primary,
                'order'      => $img->order,
            ])
            ),
            'primary_image'   => $this->whenLoaded('images_list',
                fn() => $this->images_list->firstWhere('is_primary', true)?->url
                    ?? $this->images_list->first()?->url
            ),
        ];
    }
}
