<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case PENDING = 'pending';
    case PICKED_UP = 'picked_up';
    case IN_TRANSIT = 'in_transit';
    case WAREHOUSE_SORTING = 'warehouse_sorting';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case FAILED_DELIVERY = 'failed_delivery';
    case RETURNED = 'returned';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Oczekuje na odbiór',
            self::PICKED_UP => 'Odebrana',
            self::IN_TRANSIT => 'W transporcie',
            self::WAREHOUSE_SORTING => 'Sortowanie w magazynie',
            self::OUT_FOR_DELIVERY => 'W doręczeniu',
            self::DELIVERED => 'Doręczona',
            self::FAILED_DELIVERY => 'Nieudana dostawa',
            self::RETURNED => 'Zwrócona',
            self::CANCELLED => 'Anulowana',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'slate',
            self::PICKED_UP => 'blue',
            self::IN_TRANSIT => 'indigo',
            self::WAREHOUSE_SORTING => 'purple',
            self::OUT_FOR_DELIVERY => 'amber',
            self::DELIVERED => 'emerald',
            self::FAILED_DELIVERY => 'red',
            self::RETURNED => 'orange',
            self::CANCELLED => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'clock',
            self::PICKED_UP => 'hand-raised',
            self::IN_TRANSIT => 'truck',
            self::WAREHOUSE_SORTING => 'building-office',
            self::OUT_FOR_DELIVERY => 'map-pin',
            self::DELIVERED => 'check-circle',
            self::FAILED_DELIVERY => 'x-circle',
            self::RETURNED => 'arrow-uturn-left',
            self::CANCELLED => 'no-symbol',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::DELIVERED,
            self::RETURNED,
            self::CANCELLED,
        ]);
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::PICKED_UP,
            self::IN_TRANSIT,
            self::WAREHOUSE_SORTING,
            self::OUT_FOR_DELIVERY,
        ]);
    }

    public function canTransitionTo(self $newStatus): bool
    {
        $allowed = $this->getAllowedTransitions();
        return in_array($newStatus, $allowed);
    }

    public function getAllowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [
                self::PICKED_UP,
                self::CANCELLED,
            ],
            self::PICKED_UP => [
                self::IN_TRANSIT,
                self::WAREHOUSE_SORTING,
                self::CANCELLED,
            ],
            self::IN_TRANSIT => [
                self::WAREHOUSE_SORTING,
                self::OUT_FOR_DELIVERY,
                self::RETURNED,
            ],
            self::WAREHOUSE_SORTING => [
                self::IN_TRANSIT,
                self::OUT_FOR_DELIVERY,
                self::RETURNED,
            ],
            self::OUT_FOR_DELIVERY => [
                self::DELIVERED,
                self::FAILED_DELIVERY,
                self::RETURNED,
            ],
            self::FAILED_DELIVERY => [
                self::OUT_FOR_DELIVERY,
                self::RETURNED,
                self::WAREHOUSE_SORTING,
            ],
            self::DELIVERED, self::RETURNED, self::CANCELLED => [],
        };
    }

    public static function getActiveStatuses(): array
    {
        return [
            self::PENDING,
            self::PICKED_UP,
            self::IN_TRANSIT,
            self::WAREHOUSE_SORTING,
            self::OUT_FOR_DELIVERY,
            self::FAILED_DELIVERY,
        ];
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->all();
    }
}

// update 61 

// update 72 

// update 104 

// update 191 

// update 256 

// update 294 

// update 388 

// u211

// u220

// u225
