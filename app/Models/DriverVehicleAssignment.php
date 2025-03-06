<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverVehicleAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'assigned_by',
        'assigned_at',
        'unassigned_at',
        'is_active',
        'start_odometer_km',
        'end_odometer_km',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    // Accessors
    public function getDistanceTraveledKmAttribute(): ?int
    {
        if (!$this->start_odometer_km || !$this->end_odometer_km) {
            return null;
        }
        return $this->end_odometer_km - $this->start_odometer_km;
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->unassigned_at) {
            return $this->assigned_at->diffForHumans(null, true) . ' (active)';
        }
        return $this->assigned_at->diffForHumans($this->unassigned_at, true);
    }

    // Methods
    public function endAssignment(?int $endOdometer = null): void
    {
        $this->update([
            'is_active' => false,
            'unassigned_at' => now(),
            'end_odometer_km' => $endOdometer,
        ]);
    }
}

// update 85 

// update 95 

// update 244 

// u100

// u115
