<?php

namespace App\Events;

use App\Models\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Vehicle $vehicle,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?float $speed = null,
        public readonly ?float $heading = null
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('vehicles.' . $this->vehicle->company_id),
            new Channel('vehicle.' . $this->vehicle->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'vehicle.location_updated';
    }

    public function broadcastWith(): array
    {
        return [
            'vehicle_id' => $this->vehicle->id,
            'registration_number' => $this->vehicle->registration_number,
            'coordinates' => [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
            ],
            'speed_kmh' => $this->speed,
            'heading' => $this->heading,
            'status' => $this->vehicle->status->value,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}

// update 146 

// update 169 

// update 194 

// update 216 

// update 305 

// u159

// u368

// u397
