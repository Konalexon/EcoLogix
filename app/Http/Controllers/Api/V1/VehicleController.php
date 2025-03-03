<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Events\VehicleLocationUpdated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class VehicleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vehicles = QueryBuilder::for(Vehicle::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::scope('available'),
                AllowedFilter::scope('operational'),
                AllowedFilter::scope('refrigerated'),
                AllowedFilter::scope('needs_inspection', 'needsInspection'),
            ])
            ->allowedSorts(['registration_number', 'status', 'type', 'max_weight_kg'])
            ->allowedIncludes(['company', 'homeWarehouse', 'currentDriverAssignment.driver'])
            ->defaultSort('registration_number')
            ->paginate($request->input('per_page', 20));

        return VehicleResource::collection($vehicles);
    }

    public function show(Vehicle $vehicle): VehicleResource
    {
        $vehicle->load([
            'company',
            'homeWarehouse',
            'currentDriverAssignment.driver.driverProfile',
            'routes' => fn($q) => $q->latest()->limit(5),
        ]);

        return new VehicleResource($vehicle);
    }

    public function updateLocation(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed_kmh' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
        ]);

        $vehicle->updateLocation(
            $validated['latitude'],
            $validated['longitude'],
            $validated['speed_kmh'] ?? null,
            $validated['heading'] ?? null
        );

        // Broadcast to WebSocket
        event(new VehicleLocationUpdated(
            $vehicle,
            $validated['latitude'],
            $validated['longitude'],
            $validated['speed_kmh'] ?? null,
            $validated['heading'] ?? null
        ));

        return response()->json([
            'message' => 'Lokalizacja zaktualizowana',
            'data' => [
                'coordinates' => $vehicle->coordinates,
                'updated_at' => $vehicle->location_updated_at->toIso8601String(),
            ],
        ]);
    }

    public function locations(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $vehicles = Vehicle::forCompany($companyId)
            ->operational()
            ->whereNotNull('current_latitude')
            ->whereNotNull('current_longitude')
            ->get(['id', 'registration_number', 'type', 'status', 'current_latitude', 'current_longitude', 'current_speed_kmh', 'current_heading', 'location_updated_at']);

        return response()->json([
            'data' => $vehicles->map(fn($v) => [
                'id' => $v->id,
                'registration_number' => $v->registration_number,
                'type' => $v->type->value,
                'status' => $v->status->value,
                'coordinates' => $v->coordinates,
                'speed_kmh' => (float) $v->current_speed_kmh,
                'heading' => (float) $v->current_heading,
                'updated_at' => $v->location_updated_at?->toIso8601String(),
            ]),
        ]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $vehicles = Vehicle::forCompany($companyId)->get();

        $stats = [
            'total' => $vehicles->count(),
            'by_status' => $vehicles->groupBy('status')->map->count(),
            'by_type' => $vehicles->groupBy('type')->map->count(),
            'utilization_percent' => $vehicles->count() > 0
                ? round(($vehicles->where('status', 'in_transit')->count() / $vehicles->count()) * 100, 1)
                : 0,
            'needs_inspection' => $vehicles->filter(fn($v) => $v->needsInspection())->count(),
            'needs_service' => $vehicles->filter(fn($v) => $v->needsService())->count(),
        ];

        return response()->json(['data' => $stats]);
    }
}

// update 247 

// update 259 

// u233

// u384

// u386
