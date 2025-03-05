<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'address' => $this->full_address,
            'city' => $this->city,
            'coordinates' => $this->coordinates,
            'capacity' => [
                'max_packages' => $this->max_capacity_packages,
                'current_occupancy' => $this->current_occupancy,
                'available' => $this->available_capacity,
                'occupancy_percent' => $this->occupancy_percent,
            ],
            'area_m2' => (float) $this->total_area_m2,
            'operating_hours' => $this->getOperatingHours(),
            'working_days' => $this->working_days,
            'is_open_now' => $this->is_open_now,
            'is_active' => $this->is_active,
            'features' => [
                'accepts_returns' => $this->accepts_returns,
                'has_refrigeration' => $this->has_refrigeration,
            ],
            'contact' => [
                'manager' => $this->manager_name,
                'phone' => $this->phone,
                'email' => $this->email,
            ],
            'company' => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}

// update 247 

// update 261 

// update 289 

// update 355 

// update 389 

// update 404 

// update 419 
