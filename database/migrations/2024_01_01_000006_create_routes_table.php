<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Basic info
            $table->string('name');
            $table->string('reference_number')->unique();
            $table->text('description')->nullable();

            // Status
            $table->string('status')->default('draft'); // draft, planned, in_progress, completed, cancelled

            // Schedule
            $table->date('scheduled_date');
            $table->time('planned_start_time');
            $table->time('planned_end_time')->nullable();
            $table->timestamp('actual_start_time')->nullable();
            $table->timestamp('actual_end_time')->nullable();

            // Metrics - Planned
            $table->integer('planned_stops')->default(0);
            $table->decimal('planned_distance_km', 10, 2)->nullable();
            $table->integer('planned_duration_minutes')->nullable();
            $table->decimal('planned_fuel_liters', 8, 2)->nullable();

            // Metrics - Actual
            $table->integer('completed_stops')->default(0);
            $table->decimal('actual_distance_km', 10, 2)->nullable();
            $table->integer('actual_duration_minutes')->nullable();
            $table->decimal('actual_fuel_liters', 8, 2)->nullable();

            // Route geometry (stored as JSON array of coordinates)
            $table->json('planned_polyline')->nullable();
            $table->json('actual_polyline')->nullable();

            // Start/End points
            $table->foreignId('start_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('end_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 11, 8)->nullable();
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('end_longitude', 11, 8)->nullable();

            // Load info
            $table->decimal('total_weight_kg', 10, 2)->default(0);
            $table->decimal('total_volume_m3', 8, 2)->default(0);
            $table->integer('total_packages')->default(0);

            // Optimization
            $table->boolean('is_optimized')->default(false);
            $table->timestamp('optimized_at')->nullable();
            $table->string('optimization_algorithm')->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('dispatcher_notes')->nullable();
            $table->text('driver_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['scheduled_date', 'status']);
            $table->index('driver_id');
            $table->index('vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};

// update 51 

// update 83 

// update 206 

// update 257 

// update 335 

// update 350 

// u97

// u151

// u178

// u207

// u362

// al5uyho2