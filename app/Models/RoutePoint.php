<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'shipment_id',
        'warehouse_id',
        'sequence_number',
        'optimized_sequence',
        'type',
        'address',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'contact_name',
        'contact_phone',
        'planned_arrival_at',
        'planned_departure_at',
        'planned_service_minutes',
        'actual_arrival_at',
        'actual_departure_at',
        'time_window_start',
        'time_window_end',
        'status',
        'failure_reason',
        'distance_from_previous_km',
        'duration_from_previous_minutes',
        'notes',
        'driver_notes',
        'photo_path',
        'signature_path',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'planned_arrival_at' => 'datetime',
        'planned_departure_at' => 'datetime',
        'actual_arrival_at' => 'datetime',
        'actual_departure_at' => 'datetime',
        'distance_from_previous_km' => 'decimal:2',
    ];

    // Relationships
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeDeliveries($query)
    {
        return $query->where('type', 'delivery');
    }

    public function scopePickups($query)
    {
        return $query->where('type', 'pickup');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_number');
    }

    public function scopeOptimizedOrder($query)
    {
        return $query->orderByRaw('COALESCE(optimized_sequence, sequence_number)');
    }

    // Accessors
    public function getCoordinatesAttribute(): array
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->postal_code} {$this->city}";
    }

    public function getEffectiveSequenceAttribute(): int
    {
        return $this->optimized_sequence ?? $this->sequence_number;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsWithinTimeWindowAttribute(): bool
    {
        if (!$this->time_window_start || !$this->time_window_end) {
            return true;
        }

        $now = now()->format('H:i');
        return $now >= $this->time_window_start && $now <= $this->time_window_end;
    }

    public function getActualServiceMinutesAttribute(): ?int
    {
        if (!$this->actual_arrival_at || !$this->actual_departure_at) {
            return null;
        }
        return $this->actual_arrival_at->diffInMinutes($this->actual_departure_at);
    }

    public function getDelayMinutesAttribute(): ?int
    {
        if (!$this->planned_arrival_at || !$this->actual_arrival_at) {
            return null;
        }
        return max(0, $this->planned_arrival_at->diffInMinutes($this->actual_arrival_at, false));
    }

    // Methods
    public function markArrived(): void
    {
        $this->update([
            'status' => 'arrived',
            'actual_arrival_at' => now(),
        ]);
    }

    public function markInProgress(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    public function markCompleted(?string $signaturePath = null, ?string $photoPath = null): void
    {
        $this->update([
            'status' => 'completed',
            'actual_departure_at' => now(),
            'signature_path' => $signaturePath,
            'photo_path' => $photoPath,
        ]);

        // Update shipment if this is a delivery point
        if ($this->type === 'delivery' && $this->shipment) {
            $this->shipment->transitionTo(\App\Enums\ShipmentStatus::DELIVERED);
        }
    }

    public function markFailed(string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'driver_notes' => $notes,
            'actual_departure_at' => now(),
        ]);

        // Update shipment if this is a delivery point
        if ($this->type === 'delivery' && $this->shipment) {
            $this->shipment->transitionTo(\App\Enums\ShipmentStatus::FAILED_DELIVERY);
        }
    }

    public function skip(?string $notes = null): void
    {
        $this->update([
            'status' => 'skipped',
            'driver_notes' => $notes,
        ]);
    }

    public function isDelivery(): bool
    {
        return $this->type === 'delivery';
    }

    public function isPickup(): bool
    {
        return $this->type === 'pickup';
    }
}

// update 65 

// update 71 

// update 137 

// update 266 

// u332
