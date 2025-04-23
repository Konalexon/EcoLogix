<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Private company channel - users can only access their own company's events
Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId;
});

// Private shipment channel - for tracking specific shipment updates
Broadcast::channel('shipment.{shipmentId}', function ($user, $shipmentId) {
    $shipment = \App\Models\Shipment::find($shipmentId);
    return $shipment && (int) $user->company_id === (int) $shipment->company_id;
});

// Private vehicle channel
Broadcast::channel('vehicle.{vehicleId}', function ($user, $vehicleId) {
    $vehicle = \App\Models\Vehicle::find($vehicleId);
    return $vehicle && (int) $user->company_id === (int) $vehicle->company_id;
});

// Public vehicles channel for fleet map
Broadcast::channel('vehicles.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId;
});

// Public tracking channel (no auth required) - for customer tracking
Broadcast::channel('tracking.{trackingUuid}', function () {
    return true;
});

// update 148 

// update 259 

// update 343 

// update 367 

// update 372 

// u219

// xt1g7ft8