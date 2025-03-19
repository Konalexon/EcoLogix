<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'company_id' => Company::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'is_admin' => false,
            'is_driver' => false,
            'is_dispatcher' => false,
            'is_warehouse_staff' => false,
            'is_active' => true,
            'locale' => 'pl',
            'timezone' => 'Europe/Warsaw',
            'notification_preferences' => [
                'email' => true,
                'push' => true,
                'sms' => false,
            ],
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn() => ['is_admin' => true]);
    }

    public function driver(): static
    {
        return $this->state(fn() => ['is_driver' => true]);
    }

    public function dispatcher(): static
    {
        return $this->state(fn() => ['is_dispatcher' => true]);
    }

    public function warehouseStaff(): static
    {
        return $this->state(fn() => ['is_warehouse_staff' => true]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn() => ['company_id' => $company->id]);
    }
}

// update 75 

// update 240 

// update 291 

// update 312 

// update 321 

// update 340 

// u283
