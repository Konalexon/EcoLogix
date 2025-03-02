<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use App\Services\RouteOptimizerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RouteController extends Controller
{
    public function __construct(
        private readonly RouteOptimizerService $optimizer
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $routes = QueryBuilder::for(Route::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('driver_id'),
                AllowedFilter::exact('vehicle_id'),
                AllowedFilter::scope('scheduled_for', 'scheduledFor'),
                AllowedFilter::scope('scheduled_today', 'scheduledToday'),
                AllowedFilter::scope('in_progress', 'inProgress'),
            ])
            ->allowedSorts(['scheduled_date', 'created_at', 'status'])
            ->allowedIncludes(['vehicle', 'driver', 'points', 'startWarehouse', 'endWarehouse'])
            ->defaultSort('-scheduled_date')
            ->paginate($request->input('per_page', 20));

        return RouteResource::collection($routes);
    }

    public function show(Route $route): RouteResource
    {
        $route->load([
            'vehicle',
            'driver.driverProfile',
            'points' => fn($q) => $q->orderBy('sequence_number'),
            'points.shipment',
            'startWarehouse',
            'endWarehouse',
            'shipments',
        ]);

        return new RouteResource($route);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'planned_start_time' => 'required|date_format:H:i',
            'start_warehouse_id' => 'nullable|exists:warehouses,id',
            'end_warehouse_id' => 'nullable|exists:warehouses,id',
            'notes' => 'nullable|string',
        ]);

        $route = Route::create($validated);

        return response()->json([
            'message' => 'Trasa utworzona',
            'data' => new RouteResource($route),
        ], 201);
    }

    public function optimize(Route $route): JsonResponse
    {
        if (!$route->canModify()) {
            return response()->json([
                'message' => 'Nie można zoptymalizować trasy w tym statusie',
            ], 422);
        }

        $result = $this->optimizer->optimize($route);

        return response()->json([
            'message' => 'Trasa zoptymalizowana',
            'data' => $result->toArray(),
        ]);
    }

    public function start(Route $route): JsonResponse
    {
        if (!$route->canStart()) {
            return response()->json([
                'message' => 'Nie można rozpocząć trasy - sprawdź przypisanie kierowcy i pojazdu',
            ], 422);
        }

        $route->start();

        return response()->json([
            'message' => 'Trasa rozpoczęta',
            'data' => new RouteResource($route->fresh()),
        ]);
    }

    public function complete(Route $route): JsonResponse
    {
        if (!$route->canComplete()) {
            return response()->json([
                'message' => 'Nie można zakończyć trasy w tym statusie',
            ], 422);
        }

        $route->complete();

        return response()->json([
            'message' => 'Trasa zakończona',
            'data' => new RouteResource($route->fresh()),
        ]);
    }

    public function assignDriver(Request $request, Route $route): JsonResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $route->update($validated);

        return response()->json([
            'message' => 'Kierowca przypisany',
            'data' => new RouteResource($route->load(['driver', 'vehicle'])),
        ]);
    }
}

// update 179 

// update 272 

// update 285 

// update 361 

// update 390 

// u89

// u179

// u400

// u419
