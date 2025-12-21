<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'initials' => $this->initials,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar_url,
            'roles' => [
                'is_admin' => $this->is_admin,
                'is_driver' => $this->is_driver,
                'is_dispatcher' => $this->is_dispatcher,
                'is_warehouse_staff' => $this->is_warehouse_staff,
            ],
            'is_active' => $this->is_active,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'driver_profile' => new DriverProfileResource($this->whenLoaded('driverProfile')),
            'company' => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}

// update 109 

// update 150 

// update 201 

// update 236 

// update 324 

// update 411 
