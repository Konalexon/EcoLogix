<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('home_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();

            // Identification
            $table->string('registration_number', 15)->unique();
            $table->string('vin', 17)->nullable()->unique();
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->year('year');

            // Type and capacity
            $table->string('type'); // van, truck, semi_trailer, refrigerated, motorcycle
            $table->decimal('max_weight_kg', 10, 2);
            $table->decimal('max_volume_m3', 8, 2);
            $table->decimal('length_cm', 6, 2)->nullable();
            $table->decimal('width_cm', 6, 2)->nullable();
            $table->decimal('height_cm', 6, 2)->nullable();

            // Current load
            $table->decimal('current_weight_kg', 10, 2)->default(0);
            $table->decimal('current_volume_m3', 8, 2)->default(0);

            // Fuel
            $table->string('fuel_type'); // diesel, petrol, electric, hybrid, lpg, cng
            $table->decimal('fuel_consumption_per_100km', 5, 2);
            $table->decimal('tank_capacity_liters', 6, 2)->nullable();
            $table->decimal('current_fuel_level', 5, 2)->nullable(); // percentage

            // Status and location
            $table->string('status')->default('available'); // available, in_transit, loading, unloading, maintenance, out_of_service
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->decimal('current_speed_kmh', 5, 2)->nullable();
            $table->decimal('current_heading', 5, 2)->nullable(); // degrees 0-360
            $table->timestamp('location_updated_at')->nullable();

            // Odometer
            $table->integer('odometer_km')->default(0);
            $table->integer('last_service_km')->nullable();
            $table->integer('next_service_km')->nullable();

            // Inspections
            $table->date('next_inspection_date');
            $table->date('insurance_expiry_date');

            // Features
            $table->boolean('has_gps')->default(true);
            $table->boolean('has_refrigeration')->default(false);
            $table->decimal('refrigeration_min_temp', 4, 1)->nullable();
            $table->decimal('refrigeration_max_temp', 4, 1)->nullable();
            $table->boolean('has_tail_lift')->default(false);
            $table->boolean('has_adr')->default(false); // Dangerous goods

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index('type');
            $table->index(['current_latitude', 'current_longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

// update 63 

// update 85 

// update 154 

// update 163 

// update 295 

// u361
