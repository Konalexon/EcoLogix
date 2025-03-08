<?php

namespace App\Models;

use App\Enums\DriverAvailability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'uuid',
        'company_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'avatar_path',
        'is_admin',
        'is_driver',
        'is_dispatcher',
        'is_warehouse_staff',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'locale',
        'timezone',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_driver' => 'boolean',
            'is_dispatcher' => 'boolean',
            'is_warehouse_staff' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->uuid)) {
                $user->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function driverProfile(): HasOne
    {
        return $this->hasOne(DriverProfile::class);
    }

    public function createdShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'created_by');
    }

    public function assignedShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'assigned_driver_id');
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class, 'driver_id');
    }

    public function createdRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'created_by');
    }

    public function vehicleAssignments(): HasMany
    {
        return $this->hasMany(DriverVehicleAssignment::class, 'driver_id');
    }

    public function currentVehicleAssignment(): HasOne
    {
        return $this->hasOne(DriverVehicleAssignment::class, 'driver_id')
            ->where('is_active', true)
            ->latest('assigned_at');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDrivers($query)
    {
        return $query->where('is_driver', true);
    }

    public function scopeDispatchers($query)
    {
        return $query->where('is_dispatcher', true);
    }

    public function scopeWarehouseStaff($query)
    {
        return $query->where('is_warehouse_staff', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeAvailableDrivers($query)
    {
        return $query->drivers()
            ->active()
            ->whereHas('driverProfile', function ($q) {
                $q->where('availability', DriverAvailability::AVAILABLE);
            });
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(
            mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1)
        );
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }
        return null;
    }

    // Methods
    public function isDriver(): bool
    {
        return $this->is_driver;
    }

    public function isDispatcher(): bool
    {
        return $this->is_dispatcher;
    }

    public function isWarehouseStaff(): bool
    {
        return $this->is_warehouse_staff;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function getCurrentVehicle(): ?Vehicle
    {
        return $this->currentVehicleAssignment?->vehicle;
    }

    public function getAvailability(): ?DriverAvailability
    {
        return $this->driverProfile?->availability;
    }

    public function isAvailable(): bool
    {
        return $this->getAvailability() === DriverAvailability::AVAILABLE;
    }

    public function recordLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function getTodayDeliveriesCount(): int
    {
        return $this->assignedShipments()
            ->whereDate('delivered_at', today())
            ->count();
    }
}

// update 117 

// update 144 

// update 162 

// update 247 
