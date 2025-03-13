<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'name' => env('APP_NAME', 'EcoLogix'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'Europe/Warsaw',
    'locale' => 'pl',
    'fallback_locale' => 'en',
    'faker_locale' => 'pl_PL',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => 'file',
    ],

    'providers' => ServiceProvider::defaultProviders()->merge([
        // Package Service Providers
        Barryvdh\DomPDF\ServiceProvider::class,
        Spatie\Permission\PermissionServiceProvider::class,

        // Application Service Providers
        App\Providers\AppServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
    ])->toArray(),

];

// update 155 

// update 191 

// update 400 

// update 418 

// u90

// u218
