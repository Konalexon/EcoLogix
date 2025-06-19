<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_vehicle_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            // Assignment period
            $table->timestamp('assigned_at');
            $table->timestamp('unassigned_at')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Odometer readings
            $table->integer('start_odometer_km')->nullable();
            $table->integer('end_odometer_km')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['driver_id', 'is_active']);
            $table->index(['vehicle_id', 'is_active']);
            $table->index('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_vehicle_assignments');
    }
};

// update 192 

// update 201 

// update 254 

// update 307 

// update 345 

// u216

// u231

// u290

// u297

// u315

// 6hxxrnc
// qp5b7wp