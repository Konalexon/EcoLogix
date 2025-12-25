<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('route_id')->nullable()->constrained('routes')->nullOnDelete();

            // Status change
            $table->string('from_status')->nullable();
            $table->string('to_status');

            // Event type for more granular tracking
            $table->string('event_type')->default('status_change'); // status_change, location_update, scan, note, exception

            // Location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            $table->string('location_type')->nullable(); // warehouse, vehicle, delivery_point

            // Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // Additional data
            $table->json('metadata')->nullable(); // flexible JSON for additional event data

            // Source
            $table->string('source')->default('system'); // system, driver_app, warehouse_scan, api, manual
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            // Visibility
            $table->boolean('is_public')->default(true); // show in public tracking

            $table->timestamp('created_at');

            $table->index(['shipment_id', 'created_at']);
            $table->index('event_type');
            $table->index('to_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_history');
    }
};

// update 52 

// update 85 

// update 233 

// update 262 

// update 370 

// update 406 

// u256

// u305
