<?php

namespace App\DTO;

use Illuminate\Support\Collection;

readonly class OptimizedRouteDTO
{
    public function __construct(
        public array $orderedPointIds,
        public array $orderedCoordinates,
        public float $totalDistanceKm,
        public int $estimatedDurationMinutes,
        public float $estimatedFuelLiters,
        public array $polyline,
        public array $segments,
        public string $algorithm,
    ) {
    }

    public function toArray(): array
    {
        return [
            'ordered_point_ids' => $this->orderedPointIds,
            'ordered_coordinates' => $this->orderedCoordinates,
            'total_distance_km' => round($this->totalDistanceKm, 2),
            'estimated_duration_minutes' => $this->estimatedDurationMinutes,
            'estimated_fuel_liters' => round($this->estimatedFuelLiters, 2),
            'polyline' => $this->polyline,
            'segments' => $this->segments,
            'algorithm' => $this->algorithm,
        ];
    }
}

// update 95 

// update 185 

// update 222 

// update 246 

// update 262 

// update 268 

// update 311 

// update 337 

// update 351 

// update 392 

// update 419 

// u199

// u325

// u410
