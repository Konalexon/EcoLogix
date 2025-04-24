<?php

namespace Database\Factories;

use App\Enums\DriverAvailability;
use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverProfileFactory extends Factory
{
    protected $model = DriverProfile::class;

    public function definition(): array
    {
        $licenseCategories = ['B', 'C', 'C+E', 'D'];
        $categories = fake()->randomElements($licenseCategories, rand(1, 3));
        sort($categories);

        return [
            'user_id' => User::factory()->driver(),
            'license_number' => strtoupper(fake()->regexify('[A-Z]{5}[0-9]{5}[0-9]{2}')),
            'license_categories' => array_unique($categories),
            'license_issue_date' => fake()->dateTimeBetween('-10 years', '-1 year'),
            'license_expiry_date' => fake()->dateTimeBetween('+1 month', '+5 years'),
            'license_issuing_authority' => 'Starosta ' . fake()->city(),
            'medical_certificate_expiry' => fake()->dateTimeBetween('+1 month', '+2 years'),
            'psychotechnical_test_expiry' => fake()->dateTimeBetween('+1 month', '+3 years'),
            'employment_date' => fake()->dateTimeBetween('-5 years', '-1 month'),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract']),
            'availability' => fake()->randomElement(DriverAvailability::cases()),
            'availability_changed_at' => now()->subHours(rand(1, 48)),
            'total_deliveries' => fake()->numberBetween(100, 5000),
            'successful_deliveries' => fn(array $attrs) => (int) ($attrs['total_deliveries'] * fake()->randomFloat(2, 0.9, 0.99)),
            'failed_deliveries' => fn(array $attrs) => $attrs['total_deliveries'] - $attrs['successful_deliveries'],
            'total_distance_km' => fake()->randomFloat(2, 10000, 200000),
            'total_driving_minutes' => fake()->numberBetween(10000, 200000),
            'rating' => fake()->randomFloat(2, 3.5, 5.0),
            'rating_count' => fake()->numberBetween(50, 500),
            'preferred_max_packages_per_day' => fake()->numberBetween(30, 100),
            'preferred_areas' => fake()->randomElements(['Warszawa', 'Kraków', 'Wrocław', 'Poznań', 'Gdańsk'], rand(1, 3)),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'emergency_contact_relation' => fake()->randomElement(['małżonek', 'rodzic', 'rodzeństwo', 'partner']),
        ];
    }

    public function available(): static
    {
        return $this->state(fn() => ['availability' => DriverAvailability::AVAILABLE]);
    }

    public function onRoute(): static
    {
        return $this->state(fn() => ['availability' => DriverAvailability::ON_ROUTE]);
    }

    public function topPerformer(): static
    {
        return $this->state(fn() => [
            'total_deliveries' => fake()->numberBetween(3000, 10000),
            'rating' => fake()->randomFloat(2, 4.7, 5.0),
            'rating_count' => fake()->numberBetween(300, 1000),
        ]);
    }
}

// update 135 

// update 216 

// update 392 

// u221

// u295

// 972hviq