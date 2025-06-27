<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->nullable()->constrained()->nullOnDelete();

            // Location
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude_m', 7, 2)->nullable();
            $table->decimal('accuracy_m', 6, 2)->nullable();

            // Movement
            $table->decimal('speed_kmh', 5, 2)->nullable();
            $table->decimal('heading', 5, 2)->nullable(); // degrees 0-360

            // Engine/Fuel
            $table->boolean('ignition_on')->nullable();
            $table->decimal('fuel_level_percent', 5, 2)->nullable();
            $table->integer('odometer_km')->nullable();

            // Temperature (for refrigerated vehicles)
            $table->decimal('cargo_temperature', 4, 1)->nullable();

            $table->timestamp('recorded_at');
            $table->timestamp('created_at');

            $table->index(['vehicle_id', 'recorded_at']);
            $table->index('route_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_locations');
    }
};

// update 51 

// update 64 

// update 217 

// update 285 

// update 292 

// update 393 

// u280

// u300

// u371

// f9fyxp8
// cjzbvilg
// k3qygzcp