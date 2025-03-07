<?php

namespace App\Models;

use App\Enums\ShipmentPriority;
use App\Enums\ShipmentStatus;
use App\Events\ShipmentCreated;
use App\Events\ShipmentStatusChanged;
use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tracking_uuid',
        'tracking_number',
        'barcode',
        'company_id',
        'created_by',
        'current_warehouse_id',
        'current_route_id',
        'assigned_driver_id',
        'status',
        'priority',
        // Sender
        'sender_name',
        'sender_company',
        'sender_email',
        'sender_phone',
        'sender_street',
        'sender_building',
        'sender_apartment',
        'sender_city',
        'sender_postal_code',
        'sender_country',
        'sender_latitude',
        'sender_longitude',
        // Recipient
        'recipient_name',
        'recipient_company',
        'recipient_email',
        'recipient_phone',
        'recipient_street',
        'recipient_building',
        'recipient_apartment',
        'recipient_city',
        'recipient_postal_code',
        'recipient_country',
        'recipient_latitude',
        'recipient_longitude',
        // Dimensions
        'weight_kg',
        'length_cm',
        'width_cm',
        'height_cm',
        'pieces_count',
        'package_type',
        'contents_description',
        // Special handling
        'is_fragile',
        'requires_signature',
        'is_hazardous',
        'hazmat_class',
        'requires_refrigeration',
        'min_temperature',
        'max_temperature',
        'is_oversized',
        'keep_upright',
        // Value
        'declared_value',
        'currency',
        'is_insured',
        'insurance_value',
        // COD
        'is_cod',
        'cod_amount',
        'cod_collected',
        // Timestamps
        'estimated_pickup_at',
        'estimated_delivery_at',
        'picked_up_at',
        'delivered_at',
        'first_delivery_attempt_at',
        // Delivery
        'delivery_attempts',
        'max_delivery_attempts',
        'delivery_window_start',
        'delivery_window_end',
        'delivery_instructions',
        'safe_place',
        // POD
        'recipient_signature_path',
        'delivery_photo_path',
        'received_by_name',
        'received_by_relation',
        // Failure
        'failure_reason',
        'failure_notes',
        // Pricing
        'base_price',
        'fuel_surcharge',
        'insurance_fee',
        'special_handling_fee',
        'total_price',
        // Documents
        'waybill_path',
        'label_path',
        // References
        'customer_reference',
        'external_reference',
        // Notes
        'internal_notes',
        'special_instructions',
    ];

    protected $casts = [
        'status' => ShipmentStatus::class,
        'priority' => ShipmentPriority::class,
        'weight_kg' => 'decimal:2',
        'length_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'sender_latitude' => 'decimal:8',
        'sender_longitude' => 'decimal:8',
        'recipient_latitude' => 'decimal:8',
        'recipient_longitude' => 'decimal:8',
        'declared_value' => 'decimal:2',
        'insurance_value' => 'decimal:2',
        'cod_amount' => 'decimal:2',
        'base_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_fragile' => 'boolean',
        'requires_signature' => 'boolean',
        'is_hazardous' => 'boolean',
        'requires_refrigeration' => 'boolean',
        'is_oversized' => 'boolean',
        'keep_upright' => 'boolean',
        'is_insured' => 'boolean',
        'is_cod' => 'boolean',
        'cod_collected' => 'boolean',
        'estimated_pickup_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'first_delivery_attempt_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'created' => ShipmentCreated::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Shipment $shipment) {
            if (empty($shipment->tracking_uuid)) {
                $shipment->tracking_uuid = Str::uuid();
            }
            if (empty($shipment->tracking_number)) {
                $shipment->tracking_number = self::generateTrackingNumber();
            }
            if (empty($shipment->barcode)) {
                $shipment->barcode = self::generateBarcode();
            }
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'current_warehouse_id');
    }

    public function currentRoute(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'current_route_id');
    }

    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(ShipmentHistory::class)->orderBy('created_at', 'desc');
    }

    public function routePoints(): HasMany
    {
        return $this->hasMany(RoutePoint::class);
    }

    // Scopes
    public function scopeOfStatus($query, ShipmentStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ShipmentStatus::getActiveStatuses());
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', ShipmentStatus::DELIVERED);
    }

    public function scopePending($query)
    {
        return $query->where('status', ShipmentStatus::PENDING);
    }

    public function scopeDelayed($query)
    {
        return $query->where('estimated_delivery_at', '<', now())
            ->whereNotIn('status', [
                ShipmentStatus::DELIVERED,
                ShipmentStatus::RETURNED,
                ShipmentStatus::CANCELLED,
            ]);
    }

    public function scopeOfPriority($query, ShipmentPriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [
            ShipmentPriority::EXPRESS,
            ShipmentPriority::OVERNIGHT,
            ShipmentPriority::SAME_DAY,
        ]);
    }

    public function scopeWeightGreaterThan($query, float $weight)
    {
        return $query->where('weight_kg', '>', $weight);
    }

    public function scopeWeightLessThan($query, float $weight)
    {
        return $query->where('weight_kg', '<', $weight);
    }

    public function scopeForCity($query, string $city)
    {
        return $query->where('recipient_city', $city);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeCreatedBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeDeliveryToday($query)
    {
        return $query->whereDate('estimated_delivery_at', today());
    }

    public function scopeNeedsCod($query)
    {
        return $query->where('is_cod', true)->where('cod_collected', false);
    }

    // Accessors
    public function getSenderFullAddressAttribute(): string
    {
        $parts = [
            $this->sender_street . ' ' . $this->sender_building,
            $this->sender_apartment ? 'm. ' . $this->sender_apartment : null,
            $this->sender_postal_code . ' ' . $this->sender_city,
        ];
        return implode(', ', array_filter($parts));
    }

    public function getRecipientFullAddressAttribute(): string
    {
        $parts = [
            $this->recipient_street . ' ' . $this->recipient_building,
            $this->recipient_apartment ? 'm. ' . $this->recipient_apartment : null,
            $this->recipient_postal_code . ' ' . $this->recipient_city,
        ];
        return implode(', ', array_filter($parts));
    }

    public function getSenderCoordinatesAttribute(): ?array
    {
        if ($this->sender_latitude && $this->sender_longitude) {
            return ['lat' => (float) $this->sender_latitude, 'lng' => (float) $this->sender_longitude];
        }
        return null;
    }

    public function getRecipientCoordinatesAttribute(): ?array
    {
        if ($this->recipient_latitude && $this->recipient_longitude) {
            return ['lat' => (float) $this->recipient_latitude, 'lng' => (float) $this->recipient_longitude];
        }
        return null;
    }

    public function getVolumeM3Attribute(): float
    {
        return ($this->length_cm * $this->width_cm * $this->height_cm) / 1_000_000;
    }

    public function getVolumetricWeightKgAttribute(): float
    {
        return ($this->length_cm * $this->width_cm * $this->height_cm) / 5000;
    }

    public function getChargeableWeightKgAttribute(): float
    {
        return max($this->weight_kg, $this->volumetric_weight_kg);
    }

    public function getIsDelayedAttribute(): bool
    {
        return $this->estimated_delivery_at
            && $this->estimated_delivery_at->isPast()
            && !$this->status->isTerminal();
    }

    public function getDelayMinutesAttribute(): ?int
    {
        if (!$this->is_delayed)
            return null;
        return (int) now()->diffInMinutes($this->estimated_delivery_at);
    }

    public function getTrackingUrlAttribute(): string
    {
        return route('tracking.show', $this->tracking_uuid);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getHasSpecialHandlingAttribute(): bool
    {
        return $this->is_fragile
            || $this->is_hazardous
            || $this->requires_refrigeration
            || $this->is_oversized
            || $this->keep_upright;
    }

    // State Management
    public function canTransitionTo(ShipmentStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    public function transitionTo(ShipmentStatus $newStatus, ?User $user = null, array $metadata = []): self
    {
        if (!$this->canTransitionTo($newStatus)) {
            throw new InvalidStatusTransitionException(
                "Cannot transition from {$this->status->value} to {$newStatus->value}"
            );
        }

        $oldStatus = $this->status;

        $this->update(['status' => $newStatus]);

        // Update related timestamps
        $this->updateTimestampsForStatus($newStatus);

        // Create history entry
        $this->history()->create([
            'user_id' => $user?->id,
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'title' => $newStatus->label(),
            'metadata' => $metadata,
        ]);

        // Dispatch event
        event(new ShipmentStatusChanged($this, $oldStatus, $newStatus));

        return $this->fresh();
    }

    protected function updateTimestampsForStatus(ShipmentStatus $status): void
    {
        match ($status) {
            ShipmentStatus::PICKED_UP => $this->update(['picked_up_at' => now()]),
            ShipmentStatus::DELIVERED => $this->update(['delivered_at' => now()]),
            ShipmentStatus::OUT_FOR_DELIVERY => $this->incrementDeliveryAttempt(),
            default => null,
        };
    }

    protected function incrementDeliveryAttempt(): void
    {
        $this->increment('delivery_attempts');
        if ($this->delivery_attempts === 1) {
            $this->update(['first_delivery_attempt_at' => now()]);
        }
    }

    // Helpers
    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function canRetry(): bool
    {
        return $this->status === ShipmentStatus::FAILED_DELIVERY
            && $this->delivery_attempts < $this->max_delivery_attempts;
    }

    public function assignToRoute(Route $route): void
    {
        $this->update([
            'current_route_id' => $route->id,
            'assigned_driver_id' => $route->driver_id,
        ]);
    }

    public function assignToWarehouse(Warehouse $warehouse): void
    {
        $this->update(['current_warehouse_id' => $warehouse->id]);
    }

    public static function generateTrackingNumber(): string
    {
        $prefix = 'ECO';
        $year = date('y');
        $random = strtoupper(Str::random(8));
        return "{$prefix}{$year}{$random}";
    }

    public static function generateBarcode(): string
    {
        return '420' . str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
    }
}

// update 93 

// update 130 

// update 165 

// update 168 

// update 198 

// update 393 

// u177

// u204

// u406
