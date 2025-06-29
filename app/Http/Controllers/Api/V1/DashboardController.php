<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    public function overview(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        $stats = $this->dashboardService->getOverviewStats($company);

        return response()->json(['data' => $stats]);
    }

    public function fleetUtilization(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        $data = $this->dashboardService->getFleetUtilization($company);

        return response()->json(['data' => $data]);
    }

    public function deliveryTrends(Request $request): JsonResponse
    {
        $company = $request->user()->company;
        $days = $request->input('days', 30);

        $trends = $this->dashboardService->getDeliveryTrends($company, $days);

        return response()->json(['data' => $trends]);
    }

    public function statusBreakdown(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        $breakdown = $this->dashboardService->getStatusBreakdown($company);

        return response()->json(['data' => $breakdown]);
    }

    public function topCities(Request $request): JsonResponse
    {
        $company = $request->user()->company;
        $limit = $request->input('limit', 10);

        $cities = $this->dashboardService->getTopCities($company, $limit);

        return response()->json(['data' => $cities]);
    }

    public function driverPerformance(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        $drivers = $this->dashboardService->getDriverPerformance($company);

        return response()->json(['data' => $drivers]);
    }

    public function recentActivity(Request $request): JsonResponse
    {
        $company = $request->user()->company;
        $limit = $request->input('limit', 20);

        $activity = $this->dashboardService->getRecentActivity($company, $limit);

        return response()->json(['data' => $activity]);
    }
}

// update 149 

// update 196 

// update 215 

// update 294 

// update 367 

// u320

// u339

// r9p7vac
// 7lts0wom
// a5d9do4k