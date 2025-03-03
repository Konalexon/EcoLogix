<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'license_number' => $this->license_number,
            'license_categories' => $this->license_categories,
            'license_expiry_date' => $this->license_expiry_date?->toDateString(),
            'days_until_license_expiry' => $this->days_until_license_expiry,
            'medical_certificate_expiry' => $this->medical_certificate_expiry?->toDateString(),
            'days_until_medical_expiry' => $this->days_until_medical_expiry,
            'employment_date' => $this->employment_date?->toDateString(),
            'employment_type' => $this->employment_type,
            'availability' => [
                'value' => $this->availability->value,
                'label' => $this->availability->label(),
                'color' => $this->availability->color(),
                'can_assign_route' => $this->availability->canAssignRoute(),
            ],
            'statistics' => [
                'total_deliveries' => $this->total_deliveries,
                'successful_deliveries' => $this->successful_deliveries,
                'failed_deliveries' => $this->failed_deliveries,
                'success_rate' => $this->success_rate,
                'total_distance_km' => (float) $this->total_distance_km,
                'total_driving_hours' => $this->total_driving_hours,
                'rating' => (float) $this->rating,
                'rating_count' => $this->rating_count,
                'formatted_rating' => $this->formatted_rating,
            ],
            'has_valid_documents' => $this->hasValidDocuments(),
            'current_vehicle' => new VehicleResource($this->whenLoaded('currentVehicle')),
            'current_route' => new RouteResource($this->whenLoaded('currentRoute')),
        ];
    }
}

// update 102 

// update 214 

// update 290 

// update 414 

// u203
