<?php

namespace App\Services;

use App\DTO\CreateShipmentDTO;

class PricingService
{
    private const BASE_RATE_PER_KG = 2.50; // PLN per kg
    private const BASE_RATE_PER_M3 = 150.00; // PLN per m3
    private const MINIMUM_PRICE = 15.00; // PLN
    private const FUEL_SURCHARGE_PERCENT = 8; // %

    public function calculate(CreateShipmentDTO $dto): array
    {
        $weightPrice = $dto->dimensions->chargeableWeightKg * self::BASE_RATE_PER_KG;
        $volumePrice = $dto->dimensions->volumeM3 * self::BASE_RATE_PER_M3;

        // Use the higher of weight or volume-based price
        $basePrice = max($weightPrice, $volumePrice, self::MINIMUM_PRICE);

        // Apply priority multiplier
        $basePrice *= $dto->priority->priceMultiplier();

        // Fuel surcharge
        $fuelSurcharge = $basePrice * (self::FUEL_SURCHARGE_PERCENT / 100);

        // Special handling fees
        $specialHandlingFee = $this->calculateSpecialHandlingFee($dto);

        // Insurance fee
        $insuranceFee = 0;
        if ($dto->declaredValue && $dto->declaredValue > 500) {
            $insuranceFee = $dto->declaredValue * 0.01; // 1% of declared value
        }

        $totalPrice = $basePrice + $fuelSurcharge + $specialHandlingFee + $insuranceFee;

        return [
            'base_price' => round($basePrice, 2),
            'fuel_surcharge' => round($fuelSurcharge, 2),
            'special_handling_fee' => round($specialHandlingFee, 2),
            'insurance_fee' => round($insuranceFee, 2),
            'total_price' => round($totalPrice, 2),
        ];
    }

    private function calculateSpecialHandlingFee(CreateShipmentDTO $dto): float
    {
        $fee = 0;

        if ($dto->isFragile) {
            $fee += 10.00;
        }

        if ($dto->requiresSignature) {
            $fee += 5.00;
        }

        if ($dto->isHazardous) {
            $fee += 50.00;
        }

        if ($dto->requiresRefrigeration) {
            $fee += 25.00;
        }

        if ($dto->dimensions->isOversized()) {
            $fee += 30.00;
        }

        if ($dto->isCod) {
            $fee += 8.00;
        }

        return $fee;
    }

    public function calculateForShipment(\App\Models\Shipment $shipment): array
    {
        $basePrice = max(
            $shipment->chargeable_weight_kg * self::BASE_RATE_PER_KG,
            $shipment->volume_m3 * self::BASE_RATE_PER_M3,
            self::MINIMUM_PRICE
        );

        $basePrice *= $shipment->priority->priceMultiplier();
        $fuelSurcharge = $basePrice * (self::FUEL_SURCHARGE_PERCENT / 100);

        $specialHandlingFee = 0;
        if ($shipment->is_fragile)
            $specialHandlingFee += 10;
        if ($shipment->requires_signature)
            $specialHandlingFee += 5;
        if ($shipment->is_hazardous)
            $specialHandlingFee += 50;
        if ($shipment->requires_refrigeration)
            $specialHandlingFee += 25;
        if ($shipment->is_oversized)
            $specialHandlingFee += 30;
        if ($shipment->is_cod)
            $specialHandlingFee += 8;

        $insuranceFee = $shipment->declared_value > 500
            ? $shipment->declared_value * 0.01
            : 0;

        return [
            'base_price' => round($basePrice, 2),
            'fuel_surcharge' => round($fuelSurcharge, 2),
            'special_handling_fee' => round($specialHandlingFee, 2),
            'insurance_fee' => round($insuranceFee, 2),
            'total_price' => round($basePrice + $fuelSurcharge + $specialHandlingFee + $insuranceFee, 2),
        ];
    }
}

// update 171 

// update 308 

// update 381 

// u378
