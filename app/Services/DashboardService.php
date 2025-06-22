<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getOverviewStats(Company $company, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->startOfMonth();
        $to = $to ?? now();

        $shipments = Shipment::forCompany($company->id)
            ->whereBetween('created_at', [$from, $to]);

        $totalShipments = $shipments->count();
        $deliveredShipments = (clone $shipments)->delivered()->count();
        $pendingShipments = Shipment::forCompany($company->id)->active()->count();
        $delayedShipments = Shipment::forCompany($company->id)->delayed()->count();

        // Calculate SLA (% delivered on time)
        $onTimeDeliveries = Shipment::forCompany($company->id)
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [$from, $to])
            ->whereColumn('delivered_at', '<=', 'estimated_delivery_at')
            ->count();

        $totalDelivered = Shipment::forCompany($company->id)
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [$from, $to])
            ->count();

        $slaPercent = $totalDelivered > 0
            ? round(($onTimeDeliveries / $totalDelivered) * 100, 1)
            : 100;

        // Average delivery time
        $avgDeliveryHours = Shipment::forCompany($company->id)
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [$from, $to])
            ->whereNotNull('picked_up_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, picked_up_at, delivered_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'total_shipments' => $totalShipments,
            'delivered_shipments' => $deliveredShipments,
            'pending_shipments' => $pendingShipments,
            'delayed_shipments' => $delayedShipments,
            'sla_percent' => $slaPercent,
            'avg_delivery_hours' => round($avgDeliveryHours ?? 0, 1),
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
        ];
    }

    public function getFleetUtilization(Company $company): array
    {
        $vehicles = $company->vehicles()->get();
        $total = $vehicles->count();

        if ($total === 0) {
            return [
                'total_vehicles' => 0,
                'available' => 0,
                'in_transit' => 0,
                'maintenance' => 0,
                'utilization_percent' => 0,
            ];
        }

        $available = $vehicles->where('status', 'available')->count();
        $inTransit = $vehicles->where('status', 'in_transit')->count();
        $maintenance = $vehicles->whereIn('status', ['maintenance', 'out_of_service'])->count();

        return [
            'total_vehicles' => $total,
            'available' => $available,
            'in_transit' => $inTransit,
            'maintenance' => $maintenance,
            'utilization_percent' => round(($inTransit / $total) * 100, 1),
            'by_type' => $vehicles->groupBy('type')->map->count(),
        ];
    }

    public function getDeliveryTrends(Company $company, int $days = 30): Collection
    {
        $from = now()->subDays($days)->startOfDay();

        return Shipment::forCompany($company->id)
            ->where('status', 'delivered')
            ->where('delivered_at', '>=', $from)
            ->selectRaw('DATE(delivered_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn($item) => [$item->date => $item->count]);
    }

    public function getStatusBreakdown(Company $company): Collection
    {
        return Shipment::forCompany($company->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->count]);
    }

    public function getPriorityBreakdown(Company $company): Collection
    {
        return Shipment::forCompany($company->id)
            ->active()
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(fn($item) => [$item->priority => $item->count]);
    }

    public function getTopCities(Company $company, int $limit = 10): Collection
    {
        return Shipment::forCompany($company->id)
            ->selectRaw('recipient_city, COUNT(*) as count')
            ->groupBy('recipient_city')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    public function getDriverPerformance(Company $company, int $limit = 10): Collection
    {
        return $company->users()
            ->drivers()
            ->with('driverProfile')
            ->whereHas('driverProfile')
            ->get()
            ->map(fn($driver) => [
                'id' => $driver->id,
                'name' => $driver->full_name,
                'total_deliveries' => $driver->driverProfile->total_deliveries,
                'success_rate' => $driver->driverProfile->success_rate,
                'rating' => $driver->driverProfile->rating,
                'availability' => $driver->driverProfile->availability->value,
            ])
            ->sortByDesc('total_deliveries')
            ->take($limit)
            ->values();
    }

    public function getRecentActivity(Company $company, int $limit = 20): Collection
    {
        return \App\Models\ShipmentHistory::whereHas(
            'shipment',
            fn($q) =>
            $q->where('company_id', $company->id)
        )
            ->with(['shipment:id,tracking_number', 'user:id,first_name,last_name'])
            ->where('event_type', 'status_change')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn($history) => [
                'id' => $history->id,
                'tracking_number' => $history->shipment->tracking_number,
                'status' => $history->to_status?->value,
                'status_label' => $history->to_status?->label(),
                'user' => $history->user?->full_name,
                'created_at' => $history->created_at->toIso8601String(),
                'relative_time' => $history->relative_time,
            ]);
    }
}

// update 135 

// update 224 

// update 236 

// update 244 

// update 298 

// update 347 

// u169

// 5jnlv3ut