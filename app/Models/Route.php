<?php

namespace App\Models;

use App\Enums\RouteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'vehicle_id',
        'driver_id',
        'created_by',
        'name',
        'reference_number',
        'description',
        'status',
        'scheduled_date',
        'planned_start_time',
        'planned_end_time',
        'actual_start_time',
        'actual_end_time',
        'planned_stops',
        'planned_distance_km',
        'planned_duration_minutes',
        'planned_fuel_liters',
        'completed_stops',
        'actual_distance_km',
        'actual_duration_minutes',
        'actual_fuel_liters',
        'planned_polyline',
        'actual_polyline',
        'start_warehouse_id',
        'end_warehouse_id',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'total_weight_kg',
        'total_volume_m3',
        'total_packages',
        'is_optimized',
        'optimized_at',
        'optimization_algorithm',
        'notes',
        'dispatcher_notes',
        'driver_notes',
    ];

    protected $casts = [
        'status' => RouteStatus::class,
        'scheduled_date' => 'date',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'planned_polyline' => 'array',
        'actual_polyline' => 'array',
        'planned_distance_km' => 'decimal:2',
        'actual_distance_km' => 'decimal:2',
        'total_weight_kg' => 'decimal:2',
        'total_volume_m3' => 'decimal:2',
        'is_optimized' => 'boolean',
        'optimized_at' => 'datetime',
        'start_latitude' => 'decimal:8',
        'start_longitude' => 'decimal:8',
        'end_latitude' => 'decimal:8',
        'end_longitude' => 'decimal:8',
    ];

    protected static function booted(): void
    {
        static::creating(function (Route $route) {
            if (empty($route->uuid)) {
                $route->uuid = Str::uuid();
            }
            if (empty($route->reference_number)) {
                $route->reference_number = self::generateReferenceNumber();
            }
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function startWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'start_warehouse_id');
    }

    public function endWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'end_warehouse_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(RoutePoint::class)->orderBy('sequence_number');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'current_route_id');
    }

    public function locationHistory(): HasMany
    {
        return $this->hasMany(VehicleLocation::class);
    }

    // Scopes
    public function scopeOfStatus($query, RouteStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopePlanned($query)
    {
        return $query->where('status', RouteStatus::PLANNED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', RouteStatus::IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', RouteStatus::COMPLETED);
    }

    public function scopeScheduledFor($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function scopeScheduledToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeNeedsOptimization($query)
    {
        return $query->where('is_optimized', false)
            ->whereIn('status', [RouteStatus::DRAFT, RouteStatus::PLANNED]);
    }

    // Accessors
    public function getProgressPercentAttribute(): float
    {
        if ($this->planned_stops <= 0)
            return 0;
        return round(($this->completed_stops / $this->planned_stops) * 100, 1);
    }

    public function getRemainingStopsAttribute(): int
    {
        return max(0, $this->planned_stops - $this->completed_stops);
    }

    public function getStartCoordinatesAttribute(): ?array
    {
        if ($this->start_latitude && $this->start_longitude) {
            return ['lat' => (float) $this->start_latitude, 'lng' => (float) $this->start_longitude];
        }
        return null;
    }

    public function getEndCoordinatesAttribute(): ?array
    {
        if ($this->end_latitude && $this->end_longitude) {
            return ['lat' => (float) $this->end_latitude, 'lng' => (float) $this->end_longitude];
        }
        return null;
    }

    public function getEstimatedEndTimeAttribute(): ?\Carbon\Carbon
    {
        if (!$this->actual_start_time || !$this->planned_duration_minutes) {
            return null;
        }
        return $this->actual_start_time->addMinutes($this->planned_duration_minutes);
    }

    public function getIsOnScheduleAttribute(): bool
    {
        if ($this->status !== RouteStatus::IN_PROGRESS)
            return true;

        $expectedProgress = $this->getExpectedProgressPercent();
        return $this->progress_percent >= ($expectedProgress - 10);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    // Methods
    public function canStart(): bool
    {
        return $this->status->canStart() && $this->driver_id && $this->vehicle_id;
    }

    public function canComplete(): bool
    {
        return $this->status->canComplete();
    }

    public function canModify(): bool
    {
        return $this->status->canModify();
    }

    public function start(): void
    {
        if (!$this->canStart()) {
            throw new \RuntimeException('Cannot start route in current state.');
        }

        $this->update([
            'status' => RouteStatus::IN_PROGRESS,
            'actual_start_time' => now(),
        ]);

        // Update vehicle status
        $this->vehicle?->update(['status' => 'in_transit']);
    }

    public function complete(): void
    {
        if (!$this->canComplete()) {
            throw new \RuntimeException('Cannot complete route in current state.');
        }

        $this->update([
            'status' => RouteStatus::COMPLETED,
            'actual_end_time' => now(),
            'actual_duration_minutes' => $this->actual_start_time
                ? now()->diffInMinutes($this->actual_start_time)
                : null,
        ]);

        // Update vehicle status
        $this->vehicle?->update(['status' => 'available']);
    }

    public function cancel(): void
    {
        if ($this->status === RouteStatus::COMPLETED) {
            throw new \RuntimeException('Cannot cancel completed route.');
        }

        $this->update(['status' => RouteStatus::CANCELLED]);
    }

    public function markPointCompleted(RoutePoint $point): void
    {
        $point->update([
            'status' => 'completed',
            'actual_departure_at' => now(),
        ]);

        $this->increment('completed_stops');

        // Check if all stops completed
        if ($this->completed_stops >= $this->planned_stops) {
            $this->complete();
        }
    }

    public function recalculateMetrics(): void
    {
        $shipments = $this->shipments;

        $this->update([
            'total_packages' => $shipments->count(),
            'total_weight_kg' => $shipments->sum('weight_kg'),
            'total_volume_m3' => $shipments->sum('volume_m3'),
            'planned_stops' => $this->points()->count(),
        ]);
    }

    protected function getExpectedProgressPercent(): float
    {
        if (!$this->actual_start_time || !$this->planned_duration_minutes) {
            return 0;
        }

        $elapsed = now()->diffInMinutes($this->actual_start_time);
        return min(100, ($elapsed / $this->planned_duration_minutes) * 100);
    }

    public static function generateReferenceNumber(): string
    {
        $prefix = 'RT';
        $date = date('ymd');
        $random = strtoupper(Str::random(4));
        return "{$prefix}{$date}{$random}";
    }
}

// update 144 

// update 213 

// update 243 

// update 253 

// update 310 

// update 316 

// update 411 

// update 414 

// u387

// 04ifygmc