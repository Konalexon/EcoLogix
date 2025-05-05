<?php

namespace App\Enums;

enum VehicleType: string
{
    case VAN = 'van';
    case TRUCK = 'truck';
    case SEMI_TRAILER = 'semi_trailer';
    case REFRIGERATED = 'refrigerated';
    case MOTORCYCLE = 'motorcycle';

    public function label(): string
    {
        return match ($this) {
            self::VAN => 'Van dostawczy',
            self::TRUCK => 'Ciężarówka',
            self::SEMI_TRAILER => 'Naczepa',
            self::REFRIGERATED => 'Chłodnia',
            self::MOTORCYCLE => 'Motocykl',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::VAN => 'truck',
            self::TRUCK => 'truck',
            self::SEMI_TRAILER => 'truck',
            self::REFRIGERATED => 'archive-box',
            self::MOTORCYCLE => 'bolt',
        };
    }

    public function defaultCapacityKg(): float
    {
        return match ($this) {
            self::VAN => 1500,
            self::TRUCK => 10000,
            self::SEMI_TRAILER => 25000,
            self::REFRIGERATED => 8000,
            self::MOTORCYCLE => 50,
        };
    }

    public function defaultVolumeM3(): float
    {
        return match ($this) {
            self::VAN => 12,
            self::TRUCK => 45,
            self::SEMI_TRAILER => 90,
            self::REFRIGERATED => 35,
            self::MOTORCYCLE => 0.5,
        };
    }

    public function requiredLicenseCategory(): string
    {
        return match ($this) {
            self::VAN => 'B',
            self::TRUCK => 'C',
            self::SEMI_TRAILER => 'CE',
            self::REFRIGERATED => 'C',
            self::MOTORCYCLE => 'A',
        };
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->all();
    }
}

// update 82 

// update 128 

// update 202 

// update 282 

// update 290 

// update 300 

// update 305 

// update 326 

// u212

// u413

// l6hux3vq