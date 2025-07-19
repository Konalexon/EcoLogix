<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'type' => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ],
            'tax_id' => $this->tax_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->full_address,
            'coordinates' => $this->coordinates,
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'logo_url' => $this->logo_path ? asset('storage/' . $this->logo_path) : null,
            'primary_color' => $this->primary_color,
            'stats' => $this->when($request->routeIs('companies.show'), [
                'active_drivers' => $this->getActiveDriversCount(),
                'available_vehicles' => $this->getAvailableVehiclesCount(),
                'today_deliveries' => $this->getTodayDeliveriesCount(),
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

// update 68 

// update 76 

// update 165 

// update 187 

// update 249 

// update 267 

// update 349 

// u104

// u304

// u388

// snnwtcwc
// 5v0ig38
// i4v1bm5