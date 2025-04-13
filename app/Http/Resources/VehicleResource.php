<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'registration_number' => $this->registration_number,
            'full_name' => $this->full_name,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,

            'type' => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
                'icon' => $this->type->icon(),
            ],

            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
                'is_operational' => $this->status->isOperational(),
            ],

            'capacity' => [
                'max_weight_kg' => (float) $this->max_weight_kg,
                'max_volume_m3' => (float) $this->max_volume_m3,
                'current_weight_kg' => (float) $this->current_weight_kg,
                'current_volume_m3' => (float) $this->current_volume_m3,
                'available_weight_kg' => $this->available_weight_kg,
                'available_volume_m3' => $this->available_volume_m3,
                'weight_capacity_percent' => $this->weight_capacity_percent,
                'volume_capacity_percent' => $this->volume_capacity_percent,
            ],

            'location' => $this->when($this->coordinates, [
                'coordinates' => $this->coordinates,
                'speed_kmh' => (float) $this->current_speed_kmh,
                'heading' => (float) $this->current_heading,
                'updated_at' => $this->location_updated_at?->toIso8601String(),
            ]),

            'fuel' => [
                'type' => [
                    'value' => $this->fuel_type->value,
                    'label' => $this->fuel_type->label(),
                    'is_eco_friendly' => $this->fuel_type->isEcoFriendly(),
                ],
                'consumption_per_100km' => (float) $this->fuel_consumption_per_100km,
                'tank_capacity_liters' => (float) $this->tank_capacity_liters,
                'current_level_percent' => (float) $this->current_fuel_level,
            ],

            'features' => [
                'has_gps' => $this->has_gps,
                'has_refrigeration' => $this->has_refrigeration,
                'has_tail_lift' => $this->has_tail_lift,
                'has_adr' => $this->has_adr,
            ],

            'maintenance' => [
                'odometer_km' => $this->odometer_km,
                'km_until_service' => $this->km_until_service,
                'days_until_inspection' => $this->days_until_inspection,
                'next_inspection_date' => $this->next_inspection_date?->toDateString(),
                'insurance_expiry_date' => $this->insurance_expiry_date?->toDateString(),
                'needs_inspection' => $this->needsInspection(),
                'needs_service' => $this->needsService(),
            ],

            'current_driver' => new UserResource($this->whenLoaded(
                'currentDriverAssignment',
                fn() =>
                $this->currentDriverAssignment?->driver
            )),

            'home_warehouse' => new WarehouseResource($this->whenLoaded('homeWarehouse')),
            'company' => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}

// update 127 

// update 227 

// update 263 

// update 333 

// u142

// u261

// u323

// 6rwzlal