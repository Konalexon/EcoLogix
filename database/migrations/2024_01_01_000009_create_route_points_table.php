<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();

            // Order in route
            $table->integer('sequence_number');
            $table->integer('optimized_sequence')->nullable();

            // Point type
            $table->enum('type', ['pickup', 'delivery', 'warehouse_stop', 'break', 'fuel_stop']);

            // Location
            $table->string('address');
            $table->string('city');
            $table->string('postal_code', 10);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Contact
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();

            // Timing - Planned
            $table->timestamp('planned_arrival_at')->nullable();
            $table->timestamp('planned_departure_at')->nullable();
            $table->integer('planned_service_minutes')->default(5);

            // Timing - Actual
            $table->timestamp('actual_arrival_at')->nullable();
            $table->timestamp('actual_departure_at')->nullable();

            // Time windows
            $table->string('time_window_start', 5)->nullable(); // HH:MM
            $table->string('time_window_end', 5)->nullable();

            // Status
            $table->enum('status', ['pending', 'arrived', 'in_progress', 'completed', 'skipped', 'failed'])->default('pending');
            $table->string('failure_reason')->nullable();

            // Distance from previous point
            $table->decimal('distance_from_previous_km', 8, 2)->nullable();
            $table->integer('duration_from_previous_minutes')->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('driver_notes')->nullable();

            // Proof
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();

            $table->timestamps();

            $table->index(['route_id', 'sequence_number']);
            $table->index(['route_id', 'status']);
            $table->index('shipment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_points');
    }
};

// update 157 

// update 263 

// update 264 

// update 319 

// update 323 

// update 342 

// u87

// u277
