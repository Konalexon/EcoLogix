<?php

namespace App\Models;

use App\Enums\DriverAvailability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'license_categories',
        'license_issue_date',
        'license_expiry_date',
        'license_issuing_authority',
        'medical_certificate_expiry',
        'psychotechnical_test_expiry',
        'employment_date',
        'employment_type',
        'availability',
        'availability_changed_at',
        'current_vehicle_id',
        'current_route_id',
        'total_deliveries',
        'successful_deliveries',
        'failed_deliveries',
        'total_distance_km',
        'total_driving_minutes',
        'rating',
        'rating_count',
        'preferred_max_packages_per_day',
        'preferred_areas',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'notes',
    ];

    protected $casts = [
        'license_categories' => 'array',
        'license_issue_date' => 'date',
        'license_expiry_date' => 'date',
        'medical_certificate_expiry' => 'date',
        'psychotechnical_test_expiry' => 'date',
        'employment_date' => 'date',
        'availability' => DriverAvailability::class,
        'availability_changed_at' => 'datetime',
        'total_distance_km' => 'decimal:2',
        'rating' => 'decimal:2',
        'preferred_areas' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'current_vehicle_id');
    }

    public function currentRoute(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'current_route_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('availability', DriverAvailability::AVAILABLE);
    }

    public function scopeOnRoute($query)
    {
        return $query->where('availability', DriverAvailability::ON_ROUTE);
    }

    public function scopeWithValidLicense($query)
    {
        return $query->where('license_expiry_date', '>', now());
    }

    public function scopeWithValidMedical($query)
    {
        return $query->where('medical_certificate_expiry', '>', now());
    }

    public function scopeCanDrive($query, string $licenseCategory)
    {
        return $query->whereJsonContains('license_categories', $licenseCategory);
    }

    // Accessors
    public function getSuccessRateAttribute(): float
    {
        if ($this->total_deliveries <= 0)
            return 100;
        return round(($this->successful_deliveries / $this->total_deliveries) * 100, 1);
    }

    public function getDaysUntilLicenseExpiryAttribute(): ?int
    {
        if (!$this->license_expiry_date)
            return null;
        return now()->diffInDays($this->license_expiry_date, false);
    }

    public function getDaysUntilMedicalExpiryAttribute(): ?int
    {
        if (!$this->medical_certificate_expiry)
            return null;
        return now()->diffInDays($this->medical_certificate_expiry, false);
    }

    public function getTotalDrivingHoursAttribute(): float
    {
        return round($this->total_driving_minutes / 60, 1);
    }

    public function getFormattedRatingAttribute(): string
    {
        return number_format($this->rating, 1) . ' ★';
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->availability === DriverAvailability::AVAILABLE;
    }

    public function isOnRoute(): bool
    {
        return $this->availability === DriverAvailability::ON_ROUTE;
    }

    public function canDriveVehicle(Vehicle $vehicle): bool
    {
        $required = $vehicle->type->requiredLicenseCategory();
        return in_array($required, $this->license_categories ?? []);
    }

    public function hasValidDocuments(): bool
    {
        return $this->license_expiry_date?->isFuture()
            && $this->medical_certificate_expiry?->isFuture();
    }

    public function setAvailability(DriverAvailability $availability): void
    {
        $this->update([
            'availability' => $availability,
            'availability_changed_at' => now(),
        ]);
    }

    public function startRoute(Route $route, Vehicle $vehicle): void
    {
        $this->update([
            'availability' => DriverAvailability::ON_ROUTE,
            'availability_changed_at' => now(),
            'current_route_id' => $route->id,
            'current_vehicle_id' => $vehicle->id,
        ]);
    }

    public function endRoute(): void
    {
        $this->update([
            'availability' => DriverAvailability::AVAILABLE,
            'availability_changed_at' => now(),
            'current_route_id' => null,
        ]);
    }

    public function recordDelivery(bool $successful): void
    {
        $this->increment('total_deliveries');

        if ($successful) {
            $this->increment('successful_deliveries');
        } else {
            $this->increment('failed_deliveries');
        }
    }

    public function addRating(float $rating): void
    {
        $newCount = $this->rating_count + 1;
        $newRating = (($this->rating * $this->rating_count) + $rating) / $newCount;

        $this->update([
            'rating' => round($newRating, 2),
            'rating_count' => $newCount,
        ]);
    }

    public function addDistance(float $distanceKm, int $drivingMinutes): void
    {
        $this->increment('total_distance_km', $distanceKm);
        $this->increment('total_driving_minutes', $drivingMinutes);
    }
}

// update 87 

// update 90 

// update 117 

// update 270 

// update 317 

// update 334 

// update 335 

// update 337 

// update 383 

// update 402 

// u394

// u409

// gqsbc2hh