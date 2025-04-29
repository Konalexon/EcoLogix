<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_type' => $this->event_type,
            'status' => $this->when($this->to_status, [
                'value' => $this->to_status?->value,
                'label' => $this->to_status?->label(),
                'color' => $this->status_color,
                'icon' => $this->status_icon,
            ]),
            'title' => $this->title,
            'description' => $this->description,
            'location' => [
                'name' => $this->location_name,
                'type' => $this->location_type,
                'coordinates' => $this->coordinates,
            ],
            'notes' => $this->notes,
            'user' => $this->when($this->user_id, fn() => [
                'id' => $this->user?->id,
                'name' => $this->user?->full_name,
            ]),
            'source' => $this->source,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_time' => $this->formatted_time,
            'relative_time' => $this->relative_time,
        ];
    }
}

// update 163 

// update 381 

// u134

// u147

// fbrxoey