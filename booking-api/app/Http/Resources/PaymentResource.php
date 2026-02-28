<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'amount'         => $this->amount,
            'currency'       => $this->currency,
            'payment_method' => $this->payment_method,
            'status'         => $this->status,
            'created_at'     => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
