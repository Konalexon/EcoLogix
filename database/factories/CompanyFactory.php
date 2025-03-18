<?php

namespace Database\Factories;

use App\Enums\CompanyType;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => fake()->company(),
            'legal_name' => fake()->company() . ' Sp. z o.o.',
            'tax_id' => fake()->regexify('[0-9]{10}'),
            'regon' => fake()->regexify('[0-9]{9}'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'street' => fake()->streetName(),
            'building_number' => fake()->buildingNumber(),
            'apartment_number' => fake()->optional(0.3)->numerify('##'),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => 'PL',
            'latitude' => fake()->latitude(49.0, 54.8),
            'longitude' => fake()->longitude(14.1, 24.1),
            'type' => fake()->randomElement(CompanyType::cases()),
            'is_active' => true,
            'is_verified' => fake()->boolean(80),
            'verified_at' => fn(array $attrs) => $attrs['is_verified'] ? now()->subDays(rand(1, 365)) : null,
            'billing_email' => fake()->companyEmail(),
            'payment_terms' => fake()->randomElement([14, 21, 30, 45, 60]),
            'credit_limit' => fake()->randomFloat(2, 5000, 100000),
            'current_balance' => fake()->randomFloat(2, 0, 50000),
            'primary_color' => fake()->hexColor(),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function carrier(): static
    {
        return $this->state(fn() => ['type' => CompanyType::CARRIER]);
    }

    public function shipper(): static
    {
        return $this->state(fn() => ['type' => CompanyType::SHIPPER]);
    }

    public function verified(): static
    {
        return $this->state(fn() => [
            'is_verified' => true,
            'verified_at' => now()->subDays(rand(1, 100)),
        ]);
    }
}

// update 72 

// update 231 

// u184
