<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('read_at');
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // What triggered the alert
            $table->nullableMorphs('alertable'); // shipment, vehicle, route, driver

            // Alert details
            $table->string('type'); // delay, exception, maintenance_due, license_expiry, sla_breach, geofence_exit
            $table->string('severity')->default('info'); // info, warning, critical
            $table->string('title');
            $table->text('message');

            // Status
            $table->boolean('is_read')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'is_read']);
            $table->index(['type', 'severity']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('notifications');
    }
};

// update 53 

// update 151 

// update 167 

// update 310 

// update 317 

// u286

// u367

// mmgbrsw9