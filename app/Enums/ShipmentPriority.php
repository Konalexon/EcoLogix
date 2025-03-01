<?php

namespace App\Enums;

enum ShipmentPriority: string
{
    case STANDARD = 'standard';
    case EXPRESS = 'express';
    case OVERNIGHT = 'overnight';
    case SAME_DAY = 'same_day';

    public function label(): string
    {
        return match ($this) {
            self::STANDARD => 'Standardowa',
            self::EXPRESS => 'Ekspresowa',
            self::OVERNIGHT => 'Nocna',
            self::SAME_DAY => 'Tego samego dnia',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::STANDARD => 'slate',
            self::EXPRESS => 'blue',
            self::OVERNIGHT => 'purple',
            self::SAME_DAY => 'red',
        };
    }

    public function maxDeliveryHours(): int
    {
        return match ($this) {
            self::STANDARD => 72,
            self::EXPRESS => 24,
            self::OVERNIGHT => 12,
            self::SAME_DAY => 6,
        };
    }

    public function priceMultiplier(): float
    {
        return match ($this) {
            self::STANDARD => 1.0,
            self::EXPRESS => 1.5,
            self::OVERNIGHT => 2.0,
            self::SAME_DAY => 3.0,
        };
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->all();
    }
}

// update 103 

// update 174 

// update 334 

// update 341 

// update 395 

// update 411 

// u144

// u195

// u352

// u408
