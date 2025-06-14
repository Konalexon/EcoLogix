<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Requests\UpdateShipmentStatusRequest;
use App\Http\Requests\BulkUpdateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\DTO\CreateShipmentDTO;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ShipmentController extends Controller
{
    public function __construct(
        private readonly ShipmentService $shipmentService
    ) {
    }

    /**
     * List shipments with advanced filtering
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $shipments = QueryBuilder::for(Shipment::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('priority'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::scope('weight_greater_than'),
                AllowedFilter::scope('weight_less_than'),
                AllowedFilter::scope('delayed', 'delayed'),
                AllowedFilter::scope('for_city', 'forCity'),
                AllowedFilter::scope('created_between', 'createdBetween'),
                AllowedFilter::scope('delivery_today', 'deliveryToday'),
                AllowedFilter::exact('assigned_driver_id'),
                AllowedFilter::exact('current_warehouse_id'),
            ])
            ->allowedSorts([
                'created_at',
                'weight_kg',
                'estimated_delivery_at',
                'priority',
                'status',
            ])
            ->allowedIncludes([
                'company',
                'creator',
                'currentWarehouse',
                'currentRoute',
                'assignedDriver',
                'history',
            ])
            ->defaultSort('-created_at')
            ->paginate($request->input('per_page', 20));

        return ShipmentResource::collection($shipments);
    }

    /**
     * Create a new shipment
     */
    public function store(CreateShipmentRequest $request): JsonResponse
    {
        $dto = CreateShipmentDTO::fromRequest($request);
        $shipment = $this->shipmentService->create($dto, $request->user());

        return response()->json([
            'message' => 'Przesyłka utworzona pomyślnie',
            'data' => new ShipmentResource($shipment->load(['company', 'history'])),
        ], 201);
    }

    /**
     * Get shipment details
     */
    public function show(Request $request, Shipment $shipment): ShipmentResource
    {
        $shipment->load([
            'company',
            'creator',
            'currentWarehouse',
            'currentRoute.vehicle',
            'currentRoute.driver',
            'assignedDriver.driverProfile',
            'history' => fn($q) => $q->orderBy('created_at', 'desc'),
        ]);

        return new ShipmentResource($shipment);
    }

    /**
     * Update shipment status
     */
    public function updateStatus(UpdateShipmentStatusRequest $request, Shipment $shipment): JsonResponse
    {
        $newStatus = ShipmentStatus::from($request->validated('status'));

        $shipment = $this->shipmentService->updateStatus(
            $shipment,
            $newStatus,
            $request->user(),
            $request->validated('metadata', [])
        );

        return response()->json([
            'message' => 'Status zaktualizowany',
            'data' => new ShipmentResource($shipment),
        ]);
    }

    /**
     * Bulk update shipment statuses
     */
    public function bulkUpdateStatus(BulkUpdateShipmentRequest $request): JsonResponse
    {
        $shipments = Shipment::whereIn('id', $request->validated('ids'))->get();
        $newStatus = ShipmentStatus::from($request->validated('status'));

        $result = $this->shipmentService->bulkUpdateStatus(
            $shipments,
            $newStatus,
            $request->user()
        );

        return response()->json([
            'message' => "Zaktualizowano {$result['success_count']} przesyłek",
            'data' => $result,
        ]);
    }

    /**
     * Get shipment statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $stats = [
            'total' => Shipment::forCompany($companyId)->count(),
            'pending' => Shipment::forCompany($companyId)->pending()->count(),
            'in_transit' => Shipment::forCompany($companyId)->ofStatus(ShipmentStatus::IN_TRANSIT)->count(),
            'delivered_today' => Shipment::forCompany($companyId)
                ->whereDate('delivered_at', today())
                ->count(),
            'delayed' => Shipment::forCompany($companyId)->delayed()->count(),
            'by_status' => Shipment::forCompany($companyId)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
        ];

        return response()->json(['data' => $stats]);
    }
}

// update 57 

// update 278 

// update 402 

// u79

// u168

// u214

// u232

// u358

// ln6d54h
// xqsxngpv