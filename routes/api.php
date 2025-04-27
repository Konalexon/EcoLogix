<?php

use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\RouteController;
use App\Http\Controllers\Api\V1\ShipmentController;
use App\Http\Controllers\Api\V1\TrackingController;
use App\Http\Controllers\Api\V1\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public tracking endpoint (no auth required)
Route::get('/tracking/{uuid}', [TrackingController::class, 'show'])
    ->name('tracking.show');

Route::post('/tracking/batch', [TrackingController::class, 'batch'])
    ->name('tracking.batch');

// Authenticated API routes
Route::middleware('auth:sanctum')->group(function () {

    // Current user
    Route::get('/user', fn(Request $request) => $request->user()->load('company'));

    // API v1
    Route::prefix('v1')->name('api.v1.')->group(function () {

        // Dashboard / Analytics
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/overview', [DashboardController::class, 'overview'])->name('overview');
            Route::get('/fleet-utilization', [DashboardController::class, 'fleetUtilization'])->name('fleet-utilization');
            Route::get('/delivery-trends', [DashboardController::class, 'deliveryTrends'])->name('delivery-trends');
            Route::get('/status-breakdown', [DashboardController::class, 'statusBreakdown'])->name('status-breakdown');
            Route::get('/top-cities', [DashboardController::class, 'topCities'])->name('top-cities');
            Route::get('/driver-performance', [DashboardController::class, 'driverPerformance'])->name('driver-performance');
            Route::get('/recent-activity', [DashboardController::class, 'recentActivity'])->name('recent-activity');
        });

        // Shipments
        Route::prefix('shipments')->name('shipments.')->group(function () {
            Route::get('/', [ShipmentController::class, 'index'])->name('index');
            Route::post('/', [ShipmentController::class, 'store'])->name('store');
            Route::get('/statistics', [ShipmentController::class, 'statistics'])->name('statistics');
            Route::get('/{shipment}', [ShipmentController::class, 'show'])->name('show');
            Route::patch('/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('update-status');
            Route::post('/bulk-status', [ShipmentController::class, 'bulkUpdateStatus'])->name('bulk-status');
        });

        // Vehicles
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [VehicleController::class, 'index'])->name('index');
            Route::get('/locations', [VehicleController::class, 'locations'])->name('locations');
            Route::get('/statistics', [VehicleController::class, 'statistics'])->name('statistics');
            Route::get('/{vehicle}', [VehicleController::class, 'show'])->name('show');
            Route::post('/{vehicle}/location', [VehicleController::class, 'updateLocation'])->name('update-location');
        });

        // Routes
        Route::prefix('routes')->name('routes.')->group(function () {
            Route::get('/', [RouteController::class, 'index'])->name('index');
            Route::post('/', [RouteController::class, 'store'])->name('store');
            Route::get('/{route}', [RouteController::class, 'show'])->name('show');
            Route::post('/{route}/optimize', [RouteController::class, 'optimize'])->name('optimize');
            Route::post('/{route}/start', [RouteController::class, 'start'])->name('start');
            Route::post('/{route}/complete', [RouteController::class, 'complete'])->name('complete');
            Route::post('/{route}/assign-driver', [RouteController::class, 'assignDriver'])->name('assign-driver');
        });
    });
});

// update 218 

// update 222 

// update 343 

// u110

// mhtcq9p