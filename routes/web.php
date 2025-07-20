<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', fn() => response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]));

// Public tracking page redirect
Route::get('/track/{uuid}', function (string $uuid) {
    // Will be handled by Vue Router SPA
    return view('app');
})->name('tracking.show');

// SPA catch-all route
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

// update 134 

// update 151 

// u106

// u236

// u383

// 2sdmpm1fh