<?php

namespace App\DTO;

readonly class DimensionsDTO
{
    public float $volumeM3;
    public float $volumetricWeightKg;
    public float $chargeableWeightKg;

    public function __construct(
        public float $weightKg,
        public float $lengthCm,
        public float $widthCm,
        public float $heightCm,
    ) {
        $this->volumeM3 = ($lengthCm * $widthCm * $heightCm) / 1_000_000;
        $this->volumetricWeightKg = ($lengthCm * $widthCm * $heightCm) / 5000;
        $this->chargeableWeightKg = max($weightKg, $this->volumetricWeightKg);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            weightKg: (float) $data['weight_kg'],
            lengthCm: (float) $data['length_cm'],
            widthCm: (float) $data['width_cm'],
            heightCm: (float) $data['height_cm'],
        );
    }

    public function toArray(): array
    {
        return [
            'weight_kg' => $this->weightKg,
            'length_cm' => $this->lengthCm,
            'width_cm' => $this->widthCm,
            'height_cm' => $this->heightCm,
            'volume_m3' => $this->volumeM3,
            'volumetric_weight_kg' => $this->volumetricWeightKg,
            'chargeable_weight_kg' => $this->chargeableWeightKg,
        ];
    }

    public function isOversized(): bool
    {
        // Standard parcel limits
        return $this->lengthCm > 200
            || $this->widthCm > 80
            || $this->heightCm > 60
            || ($this->lengthCm + 2 * ($this->widthCm + $this->heightCm)) > 360;
    }

    public function fitInVehicle(float $maxWeightKg, float $maxVolumeM3): bool
    {
        return $this->weightKg <= $maxWeightKg && $this->volumeM3 <= $maxVolumeM3;
    }
}

// update 64 

// update 138 

// update 406 

// u129

// u342

// u375
