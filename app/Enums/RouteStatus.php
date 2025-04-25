<?php

namespace App\Enums;

enum RouteStatus: string
{
    case DRAFT = 'draft';
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Szkic',
            self::PLANNED => 'Zaplanowana',
            self::IN_PROGRESS => 'W realizacji',
            self::COMPLETED => 'Zakończona',
            self::CANCELLED => 'Anulowana',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'slate',
            self::PLANNED => 'blue',
            self::IN_PROGRESS => 'amber',
            self::COMPLETED => 'emerald',
            self::CANCELLED => 'red',
        };
    }

    public function canModify(): bool
    {
        return in_array($this, [
            self::DRAFT,
            self::PLANNED,
        ]);
    }

    public function canStart(): bool
    {
        return $this === self::PLANNED;
    }

    public function canComplete(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->all();
    }
}

// update 208 

// update 307 

// update 311 

// u95

// u128

// u343

// 5c36itp8