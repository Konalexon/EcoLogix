<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicTrackingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'tracking_number' => $this->tracking_number,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
                'icon' => $this->status->icon(),
                'is_terminal' => $this->status->isTerminal(),
            ],
            'priority' => $this->priority->label(),
            'sender' => [
                'city' => $this->sender_city,
            ],
            'recipient' => [
                'city' => $this->recipient_city,
            ],
            'delivery' => [
                'is_delayed' => $this->is_delayed,
                'estimated_at' => $this->estimated_delivery_at?->toIso8601String(),
                'delivered_at' => $this->delivered_at?->toIso8601String(),
                'delivery_attempts' => $this->delivery_attempts,
            ],
            'proof_of_delivery' => $this->when($this->status->value === 'delivered', [
                'received_by' => $this->received_by_name,
            ]),
            'timeline' => $this->history()
                ->forPublicTracking()
                ->get()
                ->map(fn($h) => [
                    'status' => $h->to_status?->value,
                    'title' => $h->title,
                    'location' => $h->location_name,
                    'timestamp' => $h->created_at?->toIso8601String(),
                ]),
        ];
    }
}

// update 74 

// update 94 

// update 99 

// update 296 

// update 317 

// update 369 

// update 378 

// u299

// opjdr73d