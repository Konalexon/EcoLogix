<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicTrackingResource;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Public tracking endpoint - no auth required
     */
    public function show(string $uuid): JsonResponse
    {
        $shipment = Shipment::where('tracking_uuid', $uuid)
            ->orWhere('tracking_number', strtoupper($uuid))
            ->firstOrFail();

        $shipment->load([
            'history' => fn($q) => $q->forPublicTracking(),
        ]);

        return response()->json([
            'data' => new PublicTrackingResource($shipment),
        ]);
    }

    /**
     * Track multiple shipments at once
     */
    public function batch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tracking_numbers' => 'required|array|max:20',
            'tracking_numbers.*' => 'required|string',
        ]);

        $shipments = Shipment::whereIn('tracking_number', $validated['tracking_numbers'])
            ->orWhereIn('tracking_uuid', $validated['tracking_numbers'])
            ->with(['history' => fn($q) => $q->forPublicTracking()])
            ->get();

        return response()->json([
            'data' => PublicTrackingResource::collection($shipments),
            'found' => $shipments->count(),
            'not_found' => count($validated['tracking_numbers']) - $shipments->count(),
        ]);
    }
}

// update 120 

// update 131 

// update 255 

// update 291 

// update 339 

// u268

// u307

// u309
