<?php

namespace Database\Factories;

use App\Enums\ShipmentPriority;
use App\Enums\ShipmentStatus;
use App\Models\Company;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        $status = fake()->randomElement(ShipmentStatus::cases());
        $priority = fake()->randomElement(ShipmentPriority::cases());
        $weight = fake()->randomFloat(2, 0.5, 500);
        $length = fake()->randomFloat(1, 10, 200);
        $width = fake()->randomFloat(1, 10, 100);
        $height = fake()->randomFloat(1, 5, 100);

        return [
            'tracking_uuid' => Str::uuid(),
            'tracking_number' => 'ECO' . date('y') . strtoupper(Str::random(8)),
            'barcode' => '420' . str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT),
            'company_id' => Company::factory(),
            'created_by' => User::factory(),
            'status' => $status,
            'priority' => $priority,

            // Sender
            'sender_name' => fake()->name(),
            'sender_company' => fake()->optional(0.6)->company(),
            'sender_email' => fake()->email(),
            'sender_phone' => fake()->phoneNumber(),
            'sender_street' => fake()->streetName(),
            'sender_building' => fake()->buildingNumber(),
            'sender_apartment' => fake()->optional(0.3)->numerify('##'),
            'sender_city' => fake()->city(),
            'sender_postal_code' => fake()->postcode(),
            'sender_country' => 'PL',
            'sender_latitude' => fake()->latitude(49.0, 54.8),
            'sender_longitude' => fake()->longitude(14.1, 24.1),

            // Recipient
            'recipient_name' => fake()->name(),
            'recipient_company' => fake()->optional(0.5)->company(),
            'recipient_email' => fake()->email(),
            'recipient_phone' => fake()->phoneNumber(),
            'recipient_street' => fake()->streetName(),
            'recipient_building' => fake()->buildingNumber(),
            'recipient_apartment' => fake()->optional(0.4)->numerify('##'),
            'recipient_city' => fake()->city(),
            'recipient_postal_code' => fake()->postcode(),
            'recipient_country' => 'PL',
            'recipient_latitude' => fake()->latitude(49.0, 54.8),
            'recipient_longitude' => fake()->longitude(14.1, 24.1),

            // Dimensions
            'weight_kg' => $weight,
            'length_cm' => $length,
            'width_cm' => $width,
            'height_cm' => $height,
            'pieces_count' => fake()->numberBetween(1, 5),

            // Special handling
            'is_fragile' => fake()->boolean(15),
            'requires_signature' => fake()->boolean(30),
            'is_hazardous' => fake()->boolean(5),
            'requires_refrigeration' => fake()->boolean(8),
            'is_oversized' => $length > 200 || $width > 80 || $height > 60,

            // Value
            'declared_value' => fake()->optional(0.4)->randomFloat(2, 100, 5000),
            'currency' => 'PLN',
            'is_cod' => fake()->boolean(20),
            'cod_amount' => fn(array $attrs) => $attrs['is_cod'] ? fake()->randomFloat(2, 50, 2000) : null,
            'cod_collected' => fn(array $attrs) => $attrs['is_cod'] && $status === ShipmentStatus::DELIVERED,

            // Timestamps
            'estimated_pickup_at' => now()->addDays(rand(0, 2)),
            'estimated_delivery_at' => now()->addDays(rand(1, 5))->addHours(rand(0, 8)),
            'picked_up_at' => fn(array $attrs) => in_array($attrs['status'], [ShipmentStatus::IN_TRANSIT, ShipmentStatus::DELIVERED])
                ? now()->subDays(rand(1, 3))
                : null,
            'delivered_at' => fn(array $attrs) => $attrs['status'] === ShipmentStatus::DELIVERED
                ? now()->subHours(rand(1, 48))
                : null,

            // Delivery
            'delivery_attempts' => fn(array $attrs) => in_array($attrs['status'], [ShipmentStatus::FAILED_DELIVERY, ShipmentStatus::DELIVERED])
                ? fake()->numberBetween(1, 3)
                : 0,
            'max_delivery_attempts' => 3,
            'delivery_instructions' => fake()->optional(0.3)->sentence(),

            // Pricing
            'base_price' => fake()->randomFloat(2, 15, 200),
            'fuel_surcharge' => fake()->randomFloat(2, 2, 20),
            'total_price' => fn(array $attrs) => $attrs['base_price'] + $attrs['fuel_surcharge'],

            // References
            'customer_reference' => fake()->optional(0.5)->regexify('[A-Z]{2,4}-[0-9]{4,8}'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn() => ['status' => ShipmentStatus::PENDING]);
    }

    public function delivered(): static
    {
        return $this->state(fn() => [
            'status' => ShipmentStatus::DELIVERED,
            'picked_up_at' => now()->subDays(2),
            'delivered_at' => now()->subHours(rand(1, 24)),
            'delivery_attempts' => 1,
        ]);
    }

    public function inTransit(): static
    {
        return $this->state(fn() => [
            'status' => ShipmentStatus::IN_TRANSIT,
            'picked_up_at' => now()->subDays(1),
        ]);
    }

    public function express(): static
    {
        return $this->state(fn() => ['priority' => ShipmentPriority::EXPRESS]);
    }

    public function sameDay(): static
    {
        return $this->state(fn() => ['priority' => ShipmentPriority::SAME_DAY]);
    }

    public function cod(float $amount = null): static
    {
        return $this->state(fn() => [
            'is_cod' => true,
            'cod_amount' => $amount ?? fake()->randomFloat(2, 100, 1000),
        ]);
    }

    public function fragile(): static
    {
        return $this->state(fn() => ['is_fragile' => true]);
    }
}

// update 77 

// update 86 

// update 183 

// update 197 

// update 203 

// update 207 

// update 213 

// update 259 

// update 271 

// update 273 

// u98

// u146
