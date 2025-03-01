<?php

namespace App\Enums;

enum CompanyType: string
{
    case SHIPPER = 'shipper';
    case CARRIER = 'carrier';
    case BOTH = 'both';

    public function label(): string
    {
        return match ($this) {
            self::SHIPPER => 'Nadawca',
            self::CARRIER => 'Przewoźnik',
            self::BOTH => 'Nadawca i Przewoźnik',
        };
    }

    public function canShip(): bool
    {
        return in_array($this, [self::SHIPPER, self::BOTH]);
    }

    public function canCarry(): bool
    {
        return in_array($this, [self::CARRIER, self::BOTH]);
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->all();
    }
}

// update 80 

// update 90 

// update 119 

// update 209 

// update 391 

// u127

// u331
