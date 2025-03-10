<?php

namespace App\Services;

use App\DTO\AddressDTO;
use App\DTO\CreateShipmentDTO;
use App\Enums\ShipmentPriority;
use App\Enums\ShipmentStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Jobs\GenerateWaybillJob;
use App\Jobs\RecalculateETAJob;
use App\Jobs\SendTrackingEmailJob;
use App\Models\Route;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    public function __construct(
        private readonly GeocodingService $geocoding,
        private readonly PricingService $pricing,
    ) {
    }

    public function create(CreateShipmentDTO $dto, User $creator): Shipment
    {
        return DB::transaction(function () use ($dto, $creator) {
            // Geocode addresses if coordinates not provided
            $senderCoords = $this->resolveCoordinates($dto->senderAddress);
            $recipientCoords = $this->resolveCoordinates($dto->recipientAddress);

            // Calculate estimated delivery
            $estimatedDelivery = $this->calculateEstimatedDelivery($dto->priority);

            // Calculate pricing
            $pricing = $this->pricing->calculate($dto);

            $shipment = Shipment::create([
                'company_id' => $dto->companyId,
                'created_by' => $creator->id,
                'priority' => $dto->priority,

                // Sender
                'sender_name' => $dto->senderAddress->name,
                'sender_company' => $dto->senderAddress->company,
                'sender_email' => $dto->senderAddress->email,
                'sender_phone' => $dto->senderAddress->phone,
                'sender_street' => $dto->senderAddress->street,
                'sender_building' => $dto->senderAddress->building,
                'sender_apartment' => $dto->senderAddress->apartment,
                'sender_city' => $dto->senderAddress->city,
                'sender_postal_code' => $dto->senderAddress->postalCode,
                'sender_country' => $dto->senderAddress->country,
                'sender_latitude' => $senderCoords['lat'] ?? null,
                'sender_longitude' => $senderCoords['lng'] ?? null,

                // Recipient
                'recipient_name' => $dto->recipientAddress->name,
                'recipient_company' => $dto->recipientAddress->company,
                'recipient_email' => $dto->recipientAddress->email,
                'recipient_phone' => $dto->recipientAddress->phone,
                'recipient_street' => $dto->recipientAddress->street,
                'recipient_building' => $dto->recipientAddress->building,
                'recipient_apartment' => $dto->recipientAddress->apartment,
                'recipient_city' => $dto->recipientAddress->city,
                'recipient_postal_code' => $dto->recipientAddress->postalCode,
                'recipient_country' => $dto->recipientAddress->country,
                'recipient_latitude' => $recipientCoords['lat'] ?? null,
                'recipient_longitude' => $recipientCoords['lng'] ?? null,

                // Dimensions
                'weight_kg' => $dto->dimensions->weightKg,
                'length_cm' => $dto->dimensions->lengthCm,
                'width_cm' => $dto->dimensions->widthCm,
                'height_cm' => $dto->dimensions->heightCm,
                'pieces_count' => $dto->piecesCount,

                // Special handling
                'is_fragile' => $dto->isFragile,
                'requires_signature' => $dto->requiresSignature,
                'is_hazardous' => $dto->isHazardous,
                'hazmat_class' => $dto->hazmatClass,
                'requires_refrigeration' => $dto->requiresRefrigeration,
                'min_temperature' => $dto->minTemperature,
                'max_temperature' => $dto->maxTemperature,
                'is_oversized' => $dto->dimensions->isOversized(),

                // Value
                'declared_value' => $dto->declaredValue,
                'is_cod' => $dto->isCod,
                'cod_amount' => $dto->codAmount,

                // Delivery
                'estimated_delivery_at' => $estimatedDelivery,
                'delivery_window_start' => $dto->deliveryWindowStart,
                'delivery_window_end' => $dto->deliveryWindowEnd,
                'special_instructions' => $dto->specialInstructions,
                'contents_description' => $dto->contentsDescription,

                // References
                'customer_reference' => $dto->customerReference,

                // Pricing
                'base_price' => $pricing['base_price'],
                'fuel_surcharge' => $pricing['fuel_surcharge'],
                'special_handling_fee' => $pricing['special_handling_fee'],
                'total_price' => $pricing['total_price'],
            ]);

            // Create initial history entry
            ShipmentHistory::createStatusChange(
                $shipment,
                ShipmentStatus::PENDING,
                null,
                $creator
            );

            // Queue jobs
            GenerateWaybillJob::dispatch($shipment);

            if ($dto->recipientAddress->email) {
                SendTrackingEmailJob::dispatch($shipment, 'created');
            }

            return $shipment;
        });
    }

    public function updateStatus(
        Shipment $shipment,
        ShipmentStatus $newStatus,
        User $user,
        array $metadata = []
    ): Shipment {
        if (!$shipment->canTransitionTo($newStatus)) {
            throw new InvalidStatusTransitionException(
                "Cannot transition from {$shipment->status->value} to {$newStatus->value}"
            );
        }

        $oldStatus = $shipment->status;

        DB::transaction(function () use ($shipment, $newStatus, $user, $metadata) {
            $shipment->update(['status' => $newStatus]);
            $this->updateTimestampsForStatus($shipment, $newStatus);

            ShipmentHistory::createStatusChange(
                $shipment,
                $newStatus,
                $shipment->getOriginal('status'),
                $user,
                $metadata
            );
        });

        // Dispatch async jobs
        if ($newStatus->isActive()) {
            RecalculateETAJob::dispatch($shipment);
        }

        if ($shipment->recipient_email) {
            SendTrackingEmailJob::dispatch($shipment, 'status_update');
        }

        return $shipment->fresh();
    }

    public function bulkUpdateStatus(
        Collection $shipments,
        ShipmentStatus $newStatus,
        User $user
    ): array {
        $success = [];
        $failed = [];

        foreach ($shipments as $shipment) {
            try {
                $this->updateStatus($shipment, $newStatus, $user);
                $success[] = $shipment->id;
            } catch (InvalidStatusTransitionException $e) {
                $failed[] = [
                    'id' => $shipment->id,
                    'tracking_number' => $shipment->tracking_number,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success_count' => count($success),
            'failed_count' => count($failed),
            'success_ids' => $success,
            'failed' => $failed,
        ];
    }

    public function assignToRoute(Shipment $shipment, Route $route): Shipment
    {
        $shipment->update([
            'current_route_id' => $route->id,
            'assigned_driver_id' => $route->driver_id,
        ]);

        $route->recalculateMetrics();

        return $shipment->fresh();
    }

    public function assignToWarehouse(Shipment $shipment, Warehouse $warehouse, User $user): Shipment
    {
        DB::transaction(function () use ($shipment, $warehouse, $user) {
            // Update previous warehouse occupancy
            if ($shipment->current_warehouse_id) {
                $shipment->currentWarehouse?->removePackages(1);
            }

            $shipment->update(['current_warehouse_id' => $warehouse->id]);
            $warehouse->addPackages(1);

            // Log scan
            ShipmentHistory::createScan($shipment, $warehouse, $user);

            // If not already in sorting status, update
            if ($shipment->status === ShipmentStatus::IN_TRANSIT) {
                $this->updateStatus($shipment, ShipmentStatus::WAREHOUSE_SORTING, $user);
            }
        });

        return $shipment->fresh();
    }

    public function markDelivered(
        Shipment $shipment,
        User $driver,
        ?string $signaturePath = null,
        ?string $photoPath = null,
        ?string $receivedByName = null,
        ?string $receivedByRelation = null
    ): Shipment {
        return DB::transaction(function () use ($shipment, $driver, $signaturePath, $photoPath, $receivedByName, $receivedByRelation) {
            $shipment->update([
                'status' => ShipmentStatus::DELIVERED,
                'delivered_at' => now(),
                'recipient_signature_path' => $signaturePath,
                'delivery_photo_path' => $photoPath,
                'received_by_name' => $receivedByName,
                'received_by_relation' => $receivedByRelation,
            ]);

            ShipmentHistory::createStatusChange(
                $shipment,
                ShipmentStatus::DELIVERED,
                $shipment->getOriginal('status'),
                $driver,
                [
                    'received_by' => $receivedByName,
                    'has_signature' => (bool) $signaturePath,
                    'has_photo' => (bool) $photoPath,
                ]
            );

            // Update driver stats
            $driver->driverProfile?->recordDelivery(true);

            // Update warehouse occupancy
            $shipment->currentWarehouse?->removePackages(1);

            if ($shipment->recipient_email) {
                SendTrackingEmailJob::dispatch($shipment, 'delivered');
            }

            return $shipment;
        });
    }

    public function markFailedDelivery(
        Shipment $shipment,
        User $driver,
        string $reason,
        ?string $notes = null
    ): Shipment {
        return DB::transaction(function () use ($shipment, $driver, $reason, $notes) {
            $shipment->update([
                'status' => ShipmentStatus::FAILED_DELIVERY,
                'failure_reason' => $reason,
                'failure_notes' => $notes,
            ]);
            $shipment->increment('delivery_attempts');

            if ($shipment->delivery_attempts === 1) {
                $shipment->update(['first_delivery_attempt_at' => now()]);
            }

            ShipmentHistory::createStatusChange(
                $shipment,
                ShipmentStatus::FAILED_DELIVERY,
                $shipment->getOriginal('status'),
                $driver,
                ['reason' => $reason, 'notes' => $notes]
            );

            // Check if max attempts reached
            if ($shipment->delivery_attempts >= $shipment->max_delivery_attempts) {
                ShipmentHistory::createException(
                    $shipment,
                    'Maksymalna liczba prób doręczenia',
                    'Przesyłka zostanie zwrócona do nadawcy.',
                    $driver
                );
            }

            // Update driver stats
            $driver->driverProfile?->recordDelivery(false);

            if ($shipment->recipient_email) {
                SendTrackingEmailJob::dispatch($shipment, 'failed_delivery');
            }

            return $shipment;
        });
    }

    private function resolveCoordinates(AddressDTO $address): array
    {
        if ($address->hasCoordinates()) {
            return $address->getCoordinates();
        }

        try {
            return $this->geocoding->geocode($address->toFullAddress());
        } catch (\Exception $e) {
            return ['lat' => null, 'lng' => null];
        }
    }

    private function calculateEstimatedDelivery(ShipmentPriority $priority): Carbon
    {
        $hours = $priority->maxDeliveryHours();
        $estimated = now()->addHours($hours);

        // Skip weekends for standard delivery
        if ($priority === ShipmentPriority::STANDARD) {
            while ($estimated->isWeekend()) {
                $estimated->addDay();
            }
        }

        return $estimated;
    }

    private function updateTimestampsForStatus(Shipment $shipment, ShipmentStatus $status): void
    {
        match ($status) {
            ShipmentStatus::PICKED_UP => $shipment->update(['picked_up_at' => now()]),
            ShipmentStatus::DELIVERED => $shipment->update(['delivered_at' => now()]),
            default => null,
        };
    }
}

// update 109 

// update 126 

// update 384 

// u205

// u247

// u403
