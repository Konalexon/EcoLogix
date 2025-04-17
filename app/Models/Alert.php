<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'company_id',
        'alertable_type',
        'alertable_id',
        'type',
        'severity',
        'title',
        'message',
        'is_read',
        'is_resolved',
        'read_at',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
        'read_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Alert $alert) {
            if (empty($alert->uuid)) {
                $alert->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeWarning($query)
    {
        return $query->where('severity', 'warning');
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // Accessors
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'critical' => 'red',
            'warning' => 'amber',
            'info' => 'blue',
            default => 'gray',
        };
    }

    public function getSeverityIconAttribute(): string
    {
        return match ($this->severity) {
            'critical' => 'exclamation-triangle',
            'warning' => 'exclamation-circle',
            'info' => 'information-circle',
            default => 'bell',
        };
    }

    // Methods
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function resolve(User $user, ?string $notes = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $user->id,
            'resolution_notes' => $notes,
            'is_read' => true,
            'read_at' => $this->read_at ?? now(),
        ]);
    }

    // Factory methods
    public static function createDelay(Shipment $shipment, int $delayMinutes): self
    {
        return self::create([
            'company_id' => $shipment->company_id,
            'alertable_type' => Shipment::class,
            'alertable_id' => $shipment->id,
            'type' => 'delay',
            'severity' => $delayMinutes > 120 ? 'critical' : 'warning',
            'title' => 'Opóźnienie przesyłki',
            'message' => "Przesyłka {$shipment->tracking_number} jest opóźniona o {$delayMinutes} minut.",
            'metadata' => ['delay_minutes' => $delayMinutes],
        ]);
    }

    public static function createMaintenanceDue(Vehicle $vehicle, int $daysUntil): self
    {
        return self::create([
            'company_id' => $vehicle->company_id,
            'alertable_type' => Vehicle::class,
            'alertable_id' => $vehicle->id,
            'type' => 'maintenance_due',
            'severity' => $daysUntil <= 7 ? 'critical' : 'warning',
            'title' => 'Zbliża się przegląd pojazdu',
            'message' => "Pojazd {$vehicle->registration_number} wymaga przeglądu za {$daysUntil} dni.",
            'metadata' => ['days_until' => $daysUntil],
        ]);
    }

    public static function createLicenseExpiry(User $driver, int $daysUntil): self
    {
        return self::create([
            'company_id' => $driver->company_id,
            'alertable_type' => User::class,
            'alertable_id' => $driver->id,
            'type' => 'license_expiry',
            'severity' => $daysUntil <= 14 ? 'critical' : 'warning',
            'title' => 'Wygasa prawo jazdy kierowcy',
            'message' => "Prawo jazdy kierowcy {$driver->full_name} wygasa za {$daysUntil} dni.",
            'metadata' => ['days_until' => $daysUntil],
        ]);
    }

    public static function createSlaBreach(Shipment $shipment): self
    {
        return self::create([
            'company_id' => $shipment->company_id,
            'alertable_type' => Shipment::class,
            'alertable_id' => $shipment->id,
            'type' => 'sla_breach',
            'severity' => 'critical',
            'title' => 'Naruszenie SLA',
            'message' => "Przesyłka {$shipment->tracking_number} przekroczyła gwarantowany czas dostawy.",
        ]);
    }
}

// update 61 

// update 92 

// update 122 

// update 132 

// update 178 

// update 252 

// update 320 

// update 335 

// update 372 

// u166

// u228

// u234

// u389

// 4vvb3z65