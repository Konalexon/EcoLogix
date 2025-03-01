<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case AVAILABLE = 'available';
    case IN_TRANSIT = 'in_transit';
    case LOADING = 'loading';
    case UNLOADING = 'unloading';
    case MAINTENANCE = 'maintenance';
    case OUT_OF_SERVICE = 'out_of_service';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Dostępny',
            self::IN_TRANSIT => 'W trasie',
            self::LOADING => 'Załadunek',
            self::UNLOADING => 'Rozładunek',
            self::MAINTENANCE => 'Serwis',
            self::OUT_OF_SERVICE => 'Wyłączony',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'emerald',
            self::IN_TRANSIT => 'blue',
            self::LOADING => 'amber',
            self::UNLOADING => 'orange',
            self::MAINTENANCE => 'purple',
            self::OUT_OF_SERVICE => 'red',
        };
    }

    public function isOperational(): bool
    {
        return !in_array($this, [
            self::MAINTENANCE,
            self::OUT_OF_SERVICE,
        ]);
    }

    public function canAcceptRoute(): bool
    {
        return $this === self::AVAILABLE;
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->all();
    }
}

// update 94 

// update 137 

// update 246 

// update 246 

// update 251 

// update 315 

// update 351 

// update 405 

// u83
