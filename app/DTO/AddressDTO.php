<?php

namespace App\DTO;

readonly class AddressDTO
{
    public function __construct(
        public string $name,
        public ?string $company,
        public ?string $email,
        public string $phone,
        public string $street,
        public string $building,
        public ?string $apartment,
        public string $city,
        public string $postalCode,
        public string $country = 'PL',
        public ?float $latitude = null,
        public ?float $longitude = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            company: $data['company'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'],
            street: $data['street'],
            building: $data['building'],
            apartment: $data['apartment'] ?? null,
            city: $data['city'],
            postalCode: $data['postal_code'],
            country: $data['country'] ?? 'PL',
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'company' => $this->company,
            'email' => $this->email,
            'phone' => $this->phone,
            'street' => $this->street,
            'building' => $this->building,
            'apartment' => $this->apartment,
            'city' => $this->city,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function toFullAddress(): string
    {
        $parts = [
            $this->street . ' ' . $this->building,
            $this->apartment ? 'm. ' . $this->apartment : null,
            $this->postalCode . ' ' . $this->city,
            $this->country !== 'PL' ? $this->country : null,
        ];

        return implode(', ', array_filter($parts));
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function getCoordinates(): ?array
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
        ];
    }
}

// update 328 

// update 404 

// u182

// u273
