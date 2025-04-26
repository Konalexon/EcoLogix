<?php

namespace App\Models;

use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Enums\FuelType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'home_warehouse_id',
        'registration_number',
        'vin',
        'brand',
        'model',
        'year',
        'type',
        'max_weight_kg',
        'max_volume_m3',
        'length_cm',
        'width_cm',
        'height_cm',
        'current_weight_kg',
        'current_volume_m3',
        'fuel_type',
        'fuel_consumption_per_100km',
        'tank_capacity_liters',
        'current_fuel_level',
        'status',
        'current_latitude',
        'current_longitude',
        'current_speed_kmh',
        'current_heading',
        'location_updated_at',
        'odometer_km',
        'last_service_km',
        'next_service_km',
        'next_inspection_date',
        'insurance_expiry_date',
        'has_gps',
        'has_refrigeration',
        'refrigeration_min_temp',
        'refrigeration_max_temp',
        'has_tail_lift',
        'has_adr',
        'notes',
    ];

    protected $casts = [
        'type' => VehicleType::class,
        'fuel_type' => FuelType::class,
        'status' => VehicleStatus::class,
        'max_weight_kg' => 'decimal:2',
        'max_volume_m3' => 'decimal:2',
        'current_weight_kg' => 'decimal:2',
        'current_volume_m3' => 'decimal:2',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'current_speed_kmh' => 'decimal:2',
        'current_heading' => 'decimal:2',
        'location_updated_at' => 'datetime',
        'next_inspection_date' => 'date',
        'insurance_expiry_date' => 'date',
        'has_gps' => 'boolean',
        'has_refrigeration' => 'boolean',
        'has_tail_lift' => 'boolean',
        'has_adr' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Vehicle $vehicle) {
            if (empty($vehicle->uuid)) {
                $vehicle->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function homeWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'home_warehouse_id');
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }

    public function driverAssignments(): HasMany
    {
        return $this->hasMany(DriverVehicleAssignment::class);
    }

    public function currentDriverAssignment(): HasOne
    {
        return $this->hasOne(DriverVehicleAssignment::class)
            ->where('is_active', true)
            ->latest('assigned_at');
    }

    public function locationHistory(): HasMany
    {
        return $this->hasMany(VehicleLocation::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', VehicleStatus::AVAILABLE);
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', VehicleStatus::IN_TRANSIT);
    }

    public function scopeOperational($query)
    {
        return $query->whereNotIn('status', [
            VehicleStatus::MAINTENANCE,
            VehicleStatus::OUT_OF_SERVICE,
        ]);
    }

    public function scopeOfType($query, VehicleType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithMinCapacity($query, float $weightKg, float $volumeM3)
    {
        return $query->where('max_weight_kg', '>=', $weightKg)
            ->where('max_volume_m3', '>=', $volumeM3);
    }

    public function scopeNeedsInspection($query, int $daysAhead = 30)
    {
        return $query->whereDate('next_inspection_date', '<=', now()->addDays($daysAhead));
    }

    public function scopeRefrigerated($query)
    {
        return $query->where('has_refrigeration', true);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} ({$this->registration_number})";
    }

    public function getCoordinatesAttribute(): ?array
    {
        if ($this->current_latitude && $this->current_longitude) {
            return [
                'lat' => (float) $this->current_latitude,
                'lng' => (float) $this->current_longitude,
            ];
        }
        return null;
    }

    public function getWeightCapacityPercentAttribute(): float
    {
        if ($this->max_weight_kg <= 0)
            return 0;
        return round(($this->current_weight_kg / $this->max_weight_kg) * 100, 1);
    }

    public function getVolumeCapacityPercentAttribute(): float
    {
        if ($this->max_volume_m3 <= 0)
            return 0;
        return round(($this->current_volume_m3 / $this->max_volume_m3) * 100, 1);
    }

    public function getAvailableWeightKgAttribute(): float
    {
        return max(0, $this->max_weight_kg - $this->current_weight_kg);
    }

    public function getAvailableVolumeM3Attribute(): float
    {
        return max(0, $this->max_volume_m3 - $this->current_volume_m3);
    }

    public function getDaysUntilInspectionAttribute(): ?int
    {
        if (!$this->next_inspection_date)
            return null;
        return now()->diffInDays($this->next_inspection_date, false);
    }

    public function getKmUntilServiceAttribute(): ?int
    {
        if (!$this->next_service_km)
            return null;
        return max(0, $this->next_service_km - $this->odometer_km);
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === VehicleStatus::AVAILABLE;
    }

    public function isOperational(): bool
    {
        return $this->status->isOperational();
    }

    public function canAcceptLoad(float $weightKg, float $volumeM3): bool
    {
        return $this->available_weight_kg >= $weightKg
            && $this->available_volume_m3 >= $volumeM3;
    }

    public function getCurrentDriver(): ?User
    {
        return $this->currentDriverAssignment?->driver;
    }

    public function updateLocation(
        float $latitude,
        float $longitude,
        ?float $speed = null,
        ?float $heading = null
    ): void {
        $this->update([
            'current_latitude' => $latitude,
            'current_longitude' => $longitude,
            'current_speed_kmh' => $speed,
            'current_heading' => $heading,
            'location_updated_at' => now(),
        ]);

        // Save to history
        $this->locationHistory()->create([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'speed_kmh' => $speed,
            'heading' => $heading,
            'recorded_at' => now(),
        ]);
    }

    public function addLoad(float $weightKg, float $volumeM3): void
    {
        $this->increment('current_weight_kg', $weightKg);
        $this->increment('current_volume_m3', $volumeM3);
    }

    public function removeLoad(float $weightKg, float $volumeM3): void
    {
        $this->decrement('current_weight_kg', min($weightKg, $this->current_weight_kg));
        $this->decrement('current_volume_m3', min($volumeM3, $this->current_volume_m3));
    }

    public function clearLoad(): void
    {
        $this->update([
            'current_weight_kg' => 0,
            'current_volume_m3' => 0,
        ]);
    }

    public function needsInspection(int $daysAhead = 30): bool
    {
        return $this->next_inspection_date
            && $this->next_inspection_date->lte(now()->addDays($daysAhead));
    }

    public function needsService(): bool
    {
        return $this->next_service_km
            && $this->odometer_km >= $this->next_service_km;
    }
}

// update 145 

// update 194 

// update 280 

// update 295 

// u103

// u189

// u314

// u337

// u364

// l1n81rm