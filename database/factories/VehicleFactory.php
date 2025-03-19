<?php

namespace Database\Factories;

use App\Enums\FuelType;
use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $type = fake()->randomElement(VehicleType::cases());
        $brands = ['Mercedes-Benz', 'MAN', 'Volvo', 'Scania', 'DAF', 'Iveco', 'Renault', 'Ford', 'Volkswagen', 'Fiat'];

        return [
            'uuid' => Str::uuid(),
            'company_id' => Company::factory(),
            'registration_number' => strtoupper(fake()->regexify('[A-Z]{2,3} [A-Z0-9]{4,5}')),
            'vin' => strtoupper(fake()->regexify('[A-HJ-NPR-Z0-9]{17}')),
            'brand' => fake()->randomElement($brands),
            'model' => fake()->word(),
            'year' => fake()->numberBetween(2015, 2024),
            'type' => $type,
            'max_weight_kg' => $type->defaultCapacityKg(),
            'max_volume_m3' => $type->defaultVolumeM3(),
            'length_cm' => fake()->numberBetween(300, 1400),
            'width_cm' => fake()->numberBetween(180, 260),
            'height_cm' => fake()->numberBetween(180, 280),
            'current_weight_kg' => 0,
            'current_volume_m3' => 0,
            'fuel_type' => fake()->randomElement(FuelType::cases()),
            'fuel_consumption_per_100km' => fake()->randomFloat(1, 8, 35),
            'tank_capacity_liters' => fake()->numberBetween(60, 500),
            'current_fuel_level' => fake()->numberBetween(20, 100),
            'status' => fake()->randomElement([VehicleStatus::AVAILABLE, VehicleStatus::AVAILABLE, VehicleStatus::IN_TRANSIT]),
            'current_latitude' => fake()->latitude(49.0, 54.8),
            'current_longitude' => fake()->longitude(14.1, 24.1),
            'current_speed_kmh' => 0,
            'location_updated_at' => now()->subMinutes(rand(1, 60)),
            'odometer_km' => fake()->numberBetween(10000, 500000),
            'last_service_km' => fn(array $attrs) => $attrs['odometer_km'] - rand(5000, 30000),
            'next_service_km' => fn(array $attrs) => $attrs['odometer_km'] + rand(5000, 20000),
            'next_inspection_date' => fake()->dateTimeBetween('now', '+1 year'),
            'insurance_expiry_date' => fake()->dateTimeBetween('now', '+1 year'),
            'has_gps' => true,
            'has_refrigeration' => fake()->boolean(20),
            'has_tail_lift' => fake()->boolean(40),
            'has_adr' => fake()->boolean(15),
        ];
    }

    public function available(): static
    {
        return $this->state(fn() => ['status' => VehicleStatus::AVAILABLE]);
    }

    public function inTransit(): static
    {
        return $this->state(fn() => [
            'status' => VehicleStatus::IN_TRANSIT,
            'current_speed_kmh' => fake()->numberBetween(50, 90),
        ]);
    }

    public function van(): static
    {
        return $this->state(fn() => ['type' => VehicleType::VAN]);
    }

    public function truck(): static
    {
        return $this->state(fn() => ['type' => VehicleType::TRUCK]);
    }

    public function refrigerated(): static
    {
        return $this->state(fn() => [
            'has_refrigeration' => true,
            'refrigeration_min_temp' => -25,
            'refrigeration_max_temp' => 8,
        ]);
    }
}

// update 291 

// update 407 

// u265

// u381
