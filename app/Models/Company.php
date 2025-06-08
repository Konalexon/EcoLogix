<?php

namespace App\Models;

use App\Enums\CompanyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'legal_name',
        'tax_id',
        'regon',
        'email',
        'phone',
        'website',
        'street',
        'building_number',
        'apartment_number',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'type',
        'is_active',
        'is_verified',
        'verified_at',
        'billing_email',
        'payment_terms',
        'credit_limit',
        'current_balance',
        'logo_path',
        'primary_color',
        'notes',
    ];

    protected $casts = [
        'type' => CompanyType::class,
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            if (empty($company->uuid)) {
                $company->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeOfType($query, CompanyType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCarriers($query)
    {
        return $query->whereIn('type', [CompanyType::CARRIER, CompanyType::BOTH]);
    }

    public function scopeShippers($query)
    {
        return $query->whereIn('type', [CompanyType::SHIPPER, CompanyType::BOTH]);
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->street . ' ' . $this->building_number,
            $this->apartment_number ? 'm. ' . $this->apartment_number : null,
            $this->postal_code . ' ' . $this->city,
            $this->country !== 'PL' ? $this->country : null,
        ];

        return implode(', ', array_filter($parts));
    }

    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }
        return null;
    }

    // Methods
    public function canShip(): bool
    {
        return $this->type->canShip();
    }

    public function canCarry(): bool
    {
        return $this->type->canCarry();
    }

    public function getActiveDriversCount(): int
    {
        return $this->users()
            ->where('is_driver', true)
            ->where('is_active', true)
            ->count();
    }

    public function getAvailableVehiclesCount(): int
    {
        return $this->vehicles()
            ->where('status', 'available')
            ->count();
    }

    public function getTodayDeliveriesCount(): int
    {
        return $this->shipments()
            ->whereDate('delivered_at', today())
            ->count();
    }
}

// update 81 

// update 231 

// update 239 

// update 418 

// u294

// u324

// u336

// nvtdgo0i
// i3peqod7
// tyvbne9