<?php

namespace App\Enums;

enum FuelType: string
{
    case DIESEL = 'diesel';
    case PETROL = 'petrol';
    case ELECTRIC = 'electric';
    case HYBRID = 'hybrid';
    case LPG = 'lpg';
    case CNG = 'cng';

    public function label(): string
    {
        return match ($this) {
            self::DIESEL => 'Diesel',
            self::PETROL => 'Benzyna',
            self::ELECTRIC => 'Elektryczny',
            self::HYBRID => 'Hybryda',
            self::LPG => 'LPG',
            self::CNG => 'CNG',
        };
    }

    public function isEcoFriendly(): bool
    {
        return in_array($this, [
            self::ELECTRIC,
            self::HYBRID,
            self::CNG,
        ]);
    }

    public function co2PerLiter(): float
    {
        return match ($this) {
            self::DIESEL => 2.64,
            self::PETROL => 2.31,
            self::ELECTRIC => 0,
            self::HYBRID => 1.5,
            self::LPG => 1.51,
            self::CNG => 1.95,
        };
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->all();
    }
}

// update 130 

// update 221 

// update 280 

// update 398 

// u150

// u262

// u263

// u270
