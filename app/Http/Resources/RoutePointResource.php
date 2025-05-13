<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoutePointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sequence' => $this->effective_sequence,
            'original_sequence' => $this->sequence_number,
            'type' => $this->type,
            'status' => $this->status,
            'is_completed' => $this->is_completed,
            'is_failed' => $this->is_failed,
            'address' => $this->full_address,
            'city' => $this->city,
            'coordinates' => $this->coordinates,
            'contact' => [
                'name' => $this->contact_name,
                'phone' => $this->contact_phone,
            ],
            'timing' => [
                'planned_arrival' => $this->planned_arrival_at?->toIso8601String(),
                'planned_departure' => $this->planned_departure_at?->toIso8601String(),
                'actual_arrival' => $this->actual_arrival_at?->toIso8601String(),
                'actual_departure' => $this->actual_departure_at?->toIso8601String(),
                'service_minutes' => $this->planned_service_minutes,
                'actual_service_minutes' => $this->actual_service_minutes,
                'delay_minutes' => $this->delay_minutes,
            ],
            'time_window' => $this->time_window_start && $this->time_window_end
                ? "{$this->time_window_start} - {$this->time_window_end}"
                : null,
            'is_within_time_window' => $this->is_within_time_window,
            'distance' => [
                'from_previous_km' => (float) $this->distance_from_previous_km,
                'duration_from_previous_minutes' => $this->duration_from_previous_minutes,
            ],
            'failure_reason' => $this->failure_reason,
            'notes' => $this->notes,
            'driver_notes' => $this->driver_notes,
            'has_photo' => (bool) $this->photo_path,
            'has_signature' => (bool) $this->signature_path,
            'shipment' => new ShipmentResource($this->whenLoaded('shipment')),
        ];
    }
}

// update 107 

// update 119 

// update 164 

// update 183 

// update 189 

// update 205 

// update 276 

// update 292 

// update 339 

// update 341 

// update 385 

// u113

// u259

// 26pi3w7l