<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference_number' => $this->reference_number,
            'name' => $this->name,
            'description' => $this->description,

            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ],

            'schedule' => [
                'date' => $this->scheduled_date?->toDateString(),
                'planned_start' => $this->planned_start_time,
                'planned_end' => $this->planned_end_time,
                'actual_start' => $this->actual_start_time?->toIso8601String(),
                'actual_end' => $this->actual_end_time?->toIso8601String(),
            ],

            'metrics' => [
                'planned' => [
                    'stops' => $this->planned_stops,
                    'distance_km' => (float) $this->planned_distance_km,
                    'duration_minutes' => $this->planned_duration_minutes,
                    'fuel_liters' => (float) $this->planned_fuel_liters,
                ],
                'actual' => [
                    'completed_stops' => $this->completed_stops,
                    'distance_km' => (float) $this->actual_distance_km,
                    'duration_minutes' => $this->actual_duration_minutes,
                    'fuel_liters' => (float) $this->actual_fuel_liters,
                ],
                'progress_percent' => $this->progress_percent,
                'remaining_stops' => $this->remaining_stops,
            ],

            'load' => [
                'total_packages' => $this->total_packages,
                'total_weight_kg' => (float) $this->total_weight_kg,
                'total_volume_m3' => (float) $this->total_volume_m3,
            ],

            'optimization' => [
                'is_optimized' => $this->is_optimized,
                'optimized_at' => $this->optimized_at?->toIso8601String(),
                'algorithm' => $this->optimization_algorithm,
            ],

            'locations' => [
                'start' => $this->start_coordinates,
                'end' => $this->end_coordinates,
            ],

            'polyline' => $this->planned_polyline,
            'is_on_schedule' => $this->is_on_schedule,

            'notes' => [
                'general' => $this->notes,
                'dispatcher' => $this->dispatcher_notes,
                'driver' => $this->driver_notes,
            ],

            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'driver' => new UserResource($this->whenLoaded('driver')),
            'points' => RoutePointResource::collection($this->whenLoaded('points')),
            'start_warehouse' => new WarehouseResource($this->whenLoaded('startWarehouse')),
            'end_warehouse' => new WarehouseResource($this->whenLoaded('endWarehouse')),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

// update 62 

// update 240 

// update 325 

// update 399 

// u269

// u306
