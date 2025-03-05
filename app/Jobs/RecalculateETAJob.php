<?php

namespace App\Jobs;

use App\Enums\ShipmentPriority;
use App\Models\Shipment;
use App\Services\GeocodingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateETAJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public readonly Shipment $shipment,
    ) {
    }

    public function handle(): void
    {
        $currentLocation = $this->getCurrentLocation();

        if (!$currentLocation) {
            return;
        }

        $destination = $this->shipment->recipient_coordinates;

        if (!$destination) {
            return;
        }

        $distanceKm = $this->calculateDistance(
            $currentLocation['lat'],
            $currentLocation['lng'],
            $destination['lat'],
            $destination['lng']
        );

        // Estimate time based on average speed (adjusted for priority)
        $avgSpeedKmh = $this->getAverageSpeed();
        $hoursRemaining = $distanceKm / $avgSpeedKmh;

        // Add buffer for stops and traffic
        $buffer = match ($this->shipment->priority) {
            ShipmentPriority::SAME_DAY => 0.2,
            ShipmentPriority::OVERNIGHT => 0.3,
            ShipmentPriority::EXPRESS => 0.4,
            default => 0.5,
        };

        $estimatedHours = $hoursRemaining * (1 + $buffer);
        $newETA = now()->addHours($estimatedHours);

        // Don't set ETA earlier than current time
        if ($newETA->isFuture()) {
            $oldETA = $this->shipment->estimated_delivery_at;

            $this->shipment->update(['estimated_delivery_at' => $newETA]);

            // If significant change, create alert
            if ($oldETA && abs($oldETA->diffInMinutes($newETA)) > 30) {
                $this->createETAChangeAlert($oldETA, $newETA);
            }
        }
    }

    private function getCurrentLocation(): ?array
    {
        // Try to get from current route's vehicle
        $route = $this->shipment->currentRoute;

        if ($route?->vehicle) {
            $vehicle = $route->vehicle;
            if ($vehicle->current_latitude && $vehicle->current_longitude) {
                return [
                    'lat' => (float) $vehicle->current_latitude,
                    'lng' => (float) $vehicle->current_longitude,
                ];
            }
        }

        // Fall back to current warehouse location
        if ($this->shipment->currentWarehouse) {
            return $this->shipment->currentWarehouse->coordinates;
        }

        // Fall back to sender location
        return $this->shipment->sender_coordinates;
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c * 1.3; // 30% road factor
    }

    private function getAverageSpeed(): float
    {
        return match ($this->shipment->priority) {
            ShipmentPriority::SAME_DAY => 50,
            ShipmentPriority::OVERNIGHT => 45,
            ShipmentPriority::EXPRESS => 40,
            default => 35,
        };
    }

    private function createETAChangeAlert(Carbon $oldETA, Carbon $newETA): void
    {
        $delayMinutes = $oldETA->diffInMinutes($newETA, false);

        if ($delayMinutes > 0) {
            \App\Models\Alert::create([
                'company_id' => $this->shipment->company_id,
                'alertable_type' => Shipment::class,
                'alertable_id' => $this->shipment->id,
                'type' => 'eta_change',
                'severity' => $delayMinutes > 60 ? 'warning' : 'info',
                'title' => 'Zmiana przewidywanego czasu dostawy',
                'message' => "ETA dla {$this->shipment->tracking_number} przesunięte o {$delayMinutes} minut.",
                'metadata' => [
                    'old_eta' => $oldETA->toIso8601String(),
                    'new_eta' => $newETA->toIso8601String(),
                    'delay_minutes' => $delayMinutes,
                ],
            ]);
        }
    }
}

// update 54 

// update 57 

// update 62 

// update 132 

// update 288 

// update 308 

// update 366 

// update 389 

// u260

// u301
