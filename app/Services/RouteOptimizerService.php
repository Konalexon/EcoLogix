<?php

namespace App\Services;

use App\DTO\OptimizedRouteDTO;
use App\Models\Route;
use App\Models\RoutePoint;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class RouteOptimizerService
{
    private const AVERAGE_SPEED_KMH = 40;
    private const SERVICE_TIME_MINUTES = 5;

    public function optimize(Route $route, string $algorithm = 'nearest_neighbor_2opt'): OptimizedRouteDTO
    {
        $points = $route->points()->orderBy('sequence_number')->get();

        if ($points->count() < 2) {
            return $this->createEmptyResult($points, $algorithm);
        }

        // Build distance matrix
        $matrix = $this->buildDistanceMatrix($points);

        // Run optimization algorithm
        $orderedIndices = match ($algorithm) {
            'nearest_neighbor' => $this->nearestNeighbor($matrix),
            'nearest_neighbor_2opt' => $this->nearestNeighborWith2Opt($matrix),
            '2opt' => $this->twoOpt($this->getInitialOrder($points->count()), $matrix),
            default => $this->nearestNeighborWith2Opt($matrix),
        };

        // Calculate metrics
        $totalDistance = $this->calculateTotalDistance($orderedIndices, $matrix);
        $duration = $this->calculateDuration($totalDistance, $points->count());
        $fuelConsumption = $this->calculateFuelConsumption($totalDistance, $route->vehicle);

        // Generate polyline
        $orderedPoints = $orderedIndices->map(fn($i) => $points[$i]);
        $polyline = $this->generatePolyline($orderedPoints);
        $segments = $this->generateSegments($orderedPoints, $matrix, $orderedIndices);

        // Update points with optimized sequence
        $this->updatePointSequences($points, $orderedIndices);

        // Update route
        $route->update([
            'is_optimized' => true,
            'optimized_at' => now(),
            'optimization_algorithm' => $algorithm,
            'planned_distance_km' => $totalDistance,
            'planned_duration_minutes' => $duration,
            'planned_fuel_liters' => $fuelConsumption,
            'planned_polyline' => $polyline,
        ]);

        return new OptimizedRouteDTO(
            orderedPointIds: $orderedPoints->pluck('id')->toArray(),
            orderedCoordinates: $orderedPoints->map(fn($p) => $p->coordinates)->toArray(),
            totalDistanceKm: $totalDistance,
            estimatedDurationMinutes: $duration,
            estimatedFuelLiters: $fuelConsumption,
            polyline: $polyline,
            segments: $segments,
            algorithm: $algorithm,
        );
    }

    private function buildDistanceMatrix(Collection $points): array
    {
        $n = $points->count();
        $matrix = array_fill(0, $n, array_fill(0, $n, 0));

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i !== $j) {
                    $matrix[$i][$j] = $this->haversineDistance(
                        $points[$i]->latitude,
                        $points[$i]->longitude,
                        $points[$j]->latitude,
                        $points[$j]->longitude
                    );
                }
            }
        }

        return $matrix;
    }

    private function nearestNeighbor(array $matrix): Collection
    {
        $n = count($matrix);
        $visited = array_fill(0, $n, false);
        $order = collect([0]); // Start from first point
        $visited[0] = true;

        for ($step = 1; $step < $n; $step++) {
            $last = $order->last();
            $nearest = -1;
            $minDist = PHP_FLOAT_MAX;

            for ($j = 0; $j < $n; $j++) {
                if (!$visited[$j] && $matrix[$last][$j] < $minDist) {
                    $minDist = $matrix[$last][$j];
                    $nearest = $j;
                }
            }

            if ($nearest >= 0) {
                $order->push($nearest);
                $visited[$nearest] = true;
            }
        }

        return $order;
    }

    private function nearestNeighborWith2Opt(array $matrix): Collection
    {
        $initialOrder = $this->nearestNeighbor($matrix);
        return $this->twoOpt($initialOrder, $matrix);
    }

    private function twoOpt(Collection $order, array $matrix): Collection
    {
        $improved = true;
        $orderArray = $order->toArray();
        $n = count($orderArray);

        while ($improved) {
            $improved = false;

            for ($i = 0; $i < $n - 2; $i++) {
                for ($j = $i + 2; $j < $n; $j++) {
                    // Skip if j is adjacent to i
                    if ($j === $i + 1)
                        continue;

                    $delta = $this->calculate2OptDelta($orderArray, $matrix, $i, $j);

                    if ($delta < -0.001) { // Small threshold for floating point
                        $orderArray = $this->apply2OptSwap($orderArray, $i, $j);
                        $improved = true;
                    }
                }
            }
        }

        return collect($orderArray);
    }

    private function calculate2OptDelta(array $order, array $matrix, int $i, int $j): float
    {
        $n = count($order);

        $a = $order[$i];
        $b = $order[$i + 1];
        $c = $order[$j];
        $d = $order[($j + 1) % $n];

        // Current distance
        $current = $matrix[$a][$b] + $matrix[$c][$d];

        // New distance after swap
        $new = $matrix[$a][$c] + $matrix[$b][$d];

        return $new - $current;
    }

    private function apply2OptSwap(array $order, int $i, int $j): array
    {
        // Reverse the segment between i+1 and j
        $newOrder = array_slice($order, 0, $i + 1);
        $reversed = array_reverse(array_slice($order, $i + 1, $j - $i));
        $remainder = array_slice($order, $j + 1);

        return array_merge($newOrder, $reversed, $remainder);
    }

    private function haversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function calculateTotalDistance(Collection $order, array $matrix): float
    {
        $total = 0;
        $orderArray = $order->toArray();

        for ($i = 0; $i < count($orderArray) - 1; $i++) {
            $total += $matrix[$orderArray[$i]][$orderArray[$i + 1]];
        }

        return round($total, 2);
    }

    private function calculateDuration(float $distanceKm, int $stopsCount): int
    {
        $drivingMinutes = ($distanceKm / self::AVERAGE_SPEED_KMH) * 60;
        $serviceMinutes = $stopsCount * self::SERVICE_TIME_MINUTES;

        return (int) round($drivingMinutes + $serviceMinutes);
    }

    private function calculateFuelConsumption(float $distanceKm, ?Vehicle $vehicle): float
    {
        $consumptionPer100km = $vehicle?->fuel_consumption_per_100km ?? 10;
        return round(($distanceKm / 100) * $consumptionPer100km, 2);
    }

    private function generatePolyline(Collection $orderedPoints): array
    {
        return $orderedPoints->map(fn($point) => [
            $point->latitude,
            $point->longitude,
        ])->toArray();
    }

    private function generateSegments(Collection $orderedPoints, array $matrix, Collection $orderedIndices): array
    {
        $segments = [];
        $orderArray = $orderedIndices->toArray();

        for ($i = 0; $i < count($orderArray) - 1; $i++) {
            $from = $orderedPoints[$i];
            $to = $orderedPoints[$i + 1];
            $distance = $matrix[$orderArray[$i]][$orderArray[$i + 1]];

            $segments[] = [
                'from_id' => $from->id,
                'to_id' => $to->id,
                'distance_km' => round($distance, 2),
                'duration_minutes' => (int) round(($distance / self::AVERAGE_SPEED_KMH) * 60),
            ];
        }

        return $segments;
    }

    private function updatePointSequences(Collection $points, Collection $orderedIndices): void
    {
        $orderedIndices->each(function ($originalIndex, $newSequence) use ($points) {
            $points[$originalIndex]->update([
                'optimized_sequence' => $newSequence + 1,
            ]);
        });
    }

    private function getInitialOrder(int $count): Collection
    {
        return collect(range(0, $count - 1));
    }

    private function createEmptyResult(Collection $points, string $algorithm): OptimizedRouteDTO
    {
        return new OptimizedRouteDTO(
            orderedPointIds: $points->pluck('id')->toArray(),
            orderedCoordinates: $points->map(fn($p) => $p->coordinates)->toArray(),
            totalDistanceKm: 0,
            estimatedDurationMinutes: 0,
            estimatedFuelLiters: 0,
            polyline: [],
            segments: [],
            algorithm: $algorithm,
        );
    }
}

// update 103 

// update 175 

// update 204 

// update 238 

// update 345 

// u229

// u252

// u345
