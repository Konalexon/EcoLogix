<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'name',
        'code',
        'type',
        'street',
        'building_number',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'max_capacity_packages',
        'current_occupancy',
        'total_area_m2',
        'opens_at',
        'closes_at',
        'working_days',
        'is_active',
        'accepts_returns',
        'has_refrigeration',
        'manager_name',
        'phone',
        'email',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_area_m2' => 'decimal:2',
        'working_days' => 'array',
        'is_active' => 'boolean',
        'accepts_returns' => 'boolean',
        'has_refrigeration' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Warehouse $warehouse) {
            if (empty($warehouse->uuid)) {
                $warehouse->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'home_warehouse_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'current_warehouse_id');
    }

    public function outboundRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'start_warehouse_id');
    }

    public function inboundRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'end_warehouse_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeHubs($query)
    {
        return $query->where('type', 'hub');
    }

    public function scopeAcceptsReturns($query)
    {
        return $query->where('accepts_returns', true);
    }

    public function scopeRefrigerated($query)
    {
        return $query->where('has_refrigeration', true);
    }

    public function scopeWithCapacity($query, int $minAvailable = 1)
    {
        return $query->whereRaw('max_capacity_packages - current_occupancy >= ?', [$minAvailable]);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeNearby($query, float $lat, float $lng, float $radiusKm = 50)
    {
        return $query->selectRaw("
            *, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance
        ", [$lat, $lng, $lat])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        return "{$this->street} {$this->building_number}, {$this->postal_code} {$this->city}";
    }

    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return ['lat' => (float) $this->latitude, 'lng' => (float) $this->longitude];
        }
        return null;
    }

    public function getOccupancyPercentAttribute(): float
    {
        if ($this->max_capacity_packages <= 0)
            return 0;
        return round(($this->current_occupancy / $this->max_capacity_packages) * 100, 1);
    }

    public function getAvailableCapacityAttribute(): int
    {
        return max(0, $this->max_capacity_packages - $this->current_occupancy);
    }

    public function getIsOpenNowAttribute(): bool
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeekIso; // 1 = Monday, 7 = Sunday

        if ($this->working_days && !in_array($dayOfWeek, $this->working_days)) {
            return false;
        }

        $currentTime = $now->format('H:i:s');
        return $currentTime >= $this->opens_at && $currentTime <= $this->closes_at;
    }

    // Methods
    public function hasCapacityFor(int $packages): bool
    {
        return $this->available_capacity >= $packages;
    }

    public function addPackages(int $count): void
    {
        $this->increment('current_occupancy', $count);
    }

    public function removePackages(int $count): void
    {
        $this->decrement('current_occupancy', min($count, $this->current_occupancy));
    }

    public function isWorkingDay(?\Carbon\Carbon $date = null): bool
    {
        $date = $date ?? now();
        $dayOfWeek = $date->dayOfWeekIso;

        if (!$this->working_days) {
            return $dayOfWeek <= 5; // Default Mon-Fri
        }

        return in_array($dayOfWeek, $this->working_days);
    }

    public function getOperatingHours(): string
    {
        return substr($this->opens_at, 0, 5) . ' - ' . substr($this->closes_at, 0, 5);
    }
}

// update 122 

// update 130 

// update 135 

// update 300 

// update 358 

// update 364 

// u152

// u279
