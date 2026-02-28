<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'check_in_date'    => $this->check_in_date->format('Y-m-d'),
            'check_out_date'   => $this->check_out_date->format('Y-m-d'),
            'total_price'      => $this->total_price,
            'status'           => $this->status,
            'payment_status'   => $this->payment_status,
            'special_requests' => $this->special_requests,
            'resource'         => new ResourceResource($this->whenLoaded('resource')),
            'user'             => new UserResource($this->whenLoaded('user')),
            'payment'          => new PaymentResource($this->whenLoaded('payment')),
            'created_at'       => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
