<?php

namespace App\Enums;

enum DriverAvailability: string
{
    case AVAILABLE = 'available';
    case ON_ROUTE = 'on_route';
    case ON_BREAK = 'on_break';
    case OFF_DUTY = 'off_duty';
    case VACATION = 'vacation';
    case SICK_LEAVE = 'sick_leave';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Dostępny',
            self::ON_ROUTE => 'W trasie',
            self::ON_BREAK => 'Na przerwie',
            self::OFF_DUTY => 'Po służbie',
            self::VACATION => 'Urlop',
            self::SICK_LEAVE => 'Zwolnienie',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'emerald',
            self::ON_ROUTE => 'blue',
            self::ON_BREAK => 'amber',
            self::OFF_DUTY => 'slate',
            self::VACATION => 'purple',
            self::SICK_LEAVE => 'red',
        };
    }

    public function canAssignRoute(): bool
    {
        return $this === self::AVAILABLE;
    }

    public function isWorking(): bool
    {
        return in_array($this, [
            self::AVAILABLE,
            self::ON_ROUTE,
            self::ON_BREAK,
        ]);
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->all();
    }
}

// update 90 

// update 99 

// update 180 

// update 283 

// u398
