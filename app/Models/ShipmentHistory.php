<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'shipment_history';

    protected $fillable = [
        'shipment_id',
        'user_id',
        'warehouse_id',
        'route_id',
        'from_status',
        'to_status',
        'event_type',
        'latitude',
        'longitude',
        'location_name',
        'location_type',
        'title',
        'description',
        'notes',
        'metadata',
        'source',
        'ip_address',
        'user_agent',
        'is_public',
        'created_at',
    ];

    protected $casts = [
        'from_status' => ShipmentStatus::class,
        'to_status' => ShipmentStatus::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ShipmentHistory $history) {
            if (empty($history->created_at)) {
                $history->created_at = now();
            }
            if (empty($history->title)) {
                $history->title = $history->to_status?->label() ?? 'Status Update';
            }
        });
    }

    // Relationships
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeStatusChanges($query)
    {
        return $query->where('event_type', 'status_change');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeForPublicTracking($query)
    {
        return $query->public()
            ->select(['id', 'to_status', 'title', 'location_name', 'created_at'])
            ->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return ['lat' => (float) $this->latitude, 'lng' => (float) $this->longitude];
        }
        return null;
    }

    public function getStatusColorAttribute(): string
    {
        return $this->to_status?->color() ?? 'gray';
    }

    public function getStatusIconAttribute(): string
    {
        return $this->to_status?->icon() ?? 'information-circle';
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('d.m.Y H:i');
    }

    public function getRelativeTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    // Factory methods
    public static function createStatusChange(
        Shipment $shipment,
        ShipmentStatus $toStatus,
        ?ShipmentStatus $fromStatus = null,
        ?User $user = null,
        array $metadata = []
    ): self {
        return self::create([
            'shipment_id' => $shipment->id,
            'user_id' => $user?->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'event_type' => 'status_change',
            'title' => $toStatus->label(),
            'metadata' => $metadata,
            'source' => 'system',
            'is_public' => true,
        ]);
    }

    public static function createScan(
        Shipment $shipment,
        Warehouse $warehouse,
        ?User $user = null,
        ?string $notes = null
    ): self {
        return self::create([
            'shipment_id' => $shipment->id,
            'user_id' => $user?->id,
            'warehouse_id' => $warehouse->id,
            'to_status' => $shipment->status,
            'event_type' => 'scan',
            'title' => 'Zeskanowano w ' . $warehouse->name,
            'location_name' => $warehouse->city,
            'location_type' => 'warehouse',
            'latitude' => $warehouse->latitude,
            'longitude' => $warehouse->longitude,
            'notes' => $notes,
            'source' => 'warehouse_scan',
            'is_public' => true,
        ]);
    }

    public static function createLocationUpdate(
        Shipment $shipment,
        float $latitude,
        float $longitude,
        string $locationName,
        ?Route $route = null
    ): self {
        return self::create([
            'shipment_id' => $shipment->id,
            'route_id' => $route?->id,
            'to_status' => $shipment->status,
            'event_type' => 'location_update',
            'title' => 'Paczka w drodze',
            'location_name' => $locationName,
            'location_type' => 'vehicle',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'source' => 'gps',
            'is_public' => true,
        ]);
    }

    public static function createNote(
        Shipment $shipment,
        string $title,
        string $description,
        ?User $user = null,
        bool $isPublic = false
    ): self {
        return self::create([
            'shipment_id' => $shipment->id,
            'user_id' => $user?->id,
            'to_status' => $shipment->status,
            'event_type' => 'note',
            'title' => $title,
            'description' => $description,
            'source' => 'manual',
            'is_public' => $isPublic,
        ]);
    }

    public static function createException(
        Shipment $shipment,
        string $title,
        string $description,
        ?User $user = null,
        array $metadata = []
    ): self {
        return self::create([
            'shipment_id' => $shipment->id,
            'user_id' => $user?->id,
            'to_status' => $shipment->status,
            'event_type' => 'exception',
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
            'source' => 'system',
            'is_public' => true,
        ]);
    }
}

// update 88 

// update 116 

// update 146 

// update 214 

// update 225 

// update 342 

// u77
