<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleLocation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'vehicle_id',
        'route_id',
        'latitude',
        'longitude',
        'altitude_m',
        'accuracy_m',
        'speed_kmh',
        'heading',
        'ignition_on',
        'fuel_level_percent',
        'odometer_km',
        'cargo_temperature',
        'recorded_at',
        'created_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'altitude_m' => 'decimal:2',
        'accuracy_m' => 'decimal:2',
        'speed_kmh' => 'decimal:2',
        'heading' => 'decimal:2',
        'ignition_on' => 'boolean',
        'fuel_level_percent' => 'decimal:2',
        'cargo_temperature' => 'decimal:1',
        'recorded_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (VehicleLocation $location) {
            if (empty($location->created_at)) {
                $location->created_at = now();
            }
        });
    }

    // Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    // Scopes
    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeForRoute($query, int $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    public function scopeRecordedBetween($query, $start, $end)
    {
        return $query->whereBetween('recorded_at', [$start, $end]);
    }

    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('recorded_at', '>=', now()->subMinutes($minutes));
    }

    public function scopeMoving($query)
    {
        return $query->where('speed_kmh', '>', 5);
    }

    public function scopeStopped($query)
    {
        return $query->where('speed_kmh', '<=', 5);
    }

    // Accessors
    public function getCoordinatesAttribute(): array
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }

    public function getIsMovingAttribute(): bool
    {
        return $this->speed_kmh > 5;
    }

    public function getCardinalDirectionAttribute(): string
    {
        if (!$this->heading)
            return 'N/A';

        $directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
        $index = round($this->heading / 45) % 8;
        return $directions[$index];
    }

    // Static methods
    public static function getLatestForVehicle(int $vehicleId): ?self
    {
        return self::forVehicle($vehicleId)
            ->orderBy('recorded_at', 'desc')
            ->first();
    }

    public static function getTrailForRoute(int $routeId): array
    {
        return self::forRoute($routeId)
            ->orderBy('recorded_at')
            ->get()
            ->map(fn($loc) => $loc->coordinates)
            ->toArray();
    }
}

// update 94 

// update 108 

// update 125 

// update 182 

// update 249 
