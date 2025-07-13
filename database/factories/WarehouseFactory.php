<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        $cities = ['Warszawa', 'Kraków', 'Łódź', 'Wrocław', 'Poznań', 'Gdańsk', 'Szczecin', 'Bydgoszcz', 'Lublin', 'Katowice'];
        $city = fake()->randomElement($cities);

        return [
            'uuid' => Str::uuid(),
            'company_id' => Company::factory(),
            'name' => 'Magazyn ' . $city,
            'code' => strtoupper(substr($city, 0, 3)) . '-' . fake()->numerify('##'),
            'type' => fake()->randomElement(['hub', 'depot', 'sortation', 'crossdock']),
            'street' => fake()->streetName(),
            'building_number' => fake()->buildingNumber(),
            'city' => $city,
            'postal_code' => fake()->postcode(),
            'country' => 'PL',
            'latitude' => fake()->latitude(49.0, 54.8),
            'longitude' => fake()->longitude(14.1, 24.1),
            'max_capacity_packages' => fake()->numberBetween(5000, 50000),
            'current_occupancy' => fn(array $attrs) => fake()->numberBetween(0, (int) ($attrs['max_capacity_packages'] * 0.8)),
            'total_area_m2' => fake()->randomFloat(2, 1000, 20000),
            'opens_at' => '06:00:00',
            'closes_at' => '22:00:00',
            'working_days' => [1, 2, 3, 4, 5, 6], // Mon-Sat
            'is_active' => true,
            'accepts_returns' => fake()->boolean(70),
            'has_refrigeration' => fake()->boolean(30),
            'manager_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
        ];
    }

    public function hub(): static
    {
        return $this->state(fn() => [
            'type' => 'hub',
            'max_capacity_packages' => fake()->numberBetween(30000, 100000),
            'total_area_m2' => fake()->randomFloat(2, 10000, 50000),
        ]);
    }

    public function depot(): static
    {
        return $this->state(fn() => [
            'type' => 'depot',
            'max_capacity_packages' => fake()->numberBetween(2000, 10000),
        ]);
    }

    public function refrigerated(): static
    {
        return $this->state(fn() => ['has_refrigeration' => true]);
    }
}

// update 186 

// update 329 

// u359

// 97bxbjed
// j2t872pe