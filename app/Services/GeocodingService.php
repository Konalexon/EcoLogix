<?php

namespace App\Services;

class GeocodingService
{
    private string $provider;

    public function __construct()
    {
        $this->provider = config('services.geocoding.provider', 'nominatim');
    }

    public function geocode(string $address): array
    {
        return match ($this->provider) {
            'google' => $this->geocodeWithGoogle($address),
            'nominatim' => $this->geocodeWithNominatim($address),
            default => $this->geocodeWithNominatim($address),
        };
    }

    public function reverseGeocode(float $latitude, float $longitude): ?string
    {
        return match ($this->provider) {
            'google' => $this->reverseGeocodeWithGoogle($latitude, $longitude),
            'nominatim' => $this->reverseGeocodeWithNominatim($latitude, $longitude),
            default => $this->reverseGeocodeWithNominatim($latitude, $longitude),
        };
    }

    private function geocodeWithNominatim(string $address): array
    {
        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
            'countrycodes' => 'pl',
        ]);

        $response = $this->makeRequest($url);

        if (empty($response)) {
            throw new \RuntimeException("Could not geocode address: {$address}");
        }

        return [
            'lat' => (float) $response[0]['lat'],
            'lng' => (float) $response[0]['lon'],
        ];
    }

    private function geocodeWithGoogle(string $address): array
    {
        $apiKey = config('services.google_maps.key');

        if (!$apiKey) {
            throw new \RuntimeException('Google Maps API key not configured');
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
            'address' => $address,
            'key' => $apiKey,
            'region' => 'pl',
        ]);

        $response = $this->makeRequest($url);

        if ($response['status'] !== 'OK' || empty($response['results'])) {
            throw new \RuntimeException("Could not geocode address: {$address}");
        }

        $location = $response['results'][0]['geometry']['location'];

        return [
            'lat' => $location['lat'],
            'lng' => $location['lng'],
        ];
    }

    private function reverseGeocodeWithNominatim(float $latitude, float $longitude): ?string
    {
        $url = 'https://nominatim.openstreetmap.org/reverse?' . http_build_query([
            'lat' => $latitude,
            'lon' => $longitude,
            'format' => 'json',
        ]);

        $response = $this->makeRequest($url);

        return $response['display_name'] ?? null;
    }

    private function reverseGeocodeWithGoogle(float $latitude, float $longitude): ?string
    {
        $apiKey = config('services.google_maps.key');

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
            'latlng' => "{$latitude},{$longitude}",
            'key' => $apiKey,
        ]);

        $response = $this->makeRequest($url);

        return $response['results'][0]['formatted_address'] ?? null;
    }

    private function makeRequest(string $url): array
    {
        $context = stream_context_create([
            'http' => [
                'header' => 'User-Agent: EcoLogix/1.0',
                'timeout' => 10,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException('Geocoding request failed');
        }

        return json_decode($response, true) ?? [];
    }
}

// update 113 
