<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Basic info
            $table->string('name');
            $table->string('code', 10)->unique(); // e.g., WAW-01, KRK-02
            $table->enum('type', ['hub', 'sorting_center', 'depot', 'cross_dock'])->default('hub');

            // Address
            $table->string('street');
            $table->string('building_number', 10);
            $table->string('city');
            $table->string('postal_code', 10);
            $table->string('country', 2)->default('PL');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Capacity
            $table->integer('max_capacity_packages')->default(10000);
            $table->integer('current_occupancy')->default(0);
            $table->decimal('total_area_m2', 10, 2)->nullable();

            // Operations
            $table->time('opens_at')->default('06:00:00');
            $table->time('closes_at')->default('22:00:00');
            $table->json('working_days')->nullable(); // [1,2,3,4,5,6] Mon-Sat
            $table->boolean('is_active')->default(true);
            $table->boolean('accepts_returns')->default(true);
            $table->boolean('has_refrigeration')->default(false);

            // Contact
            $table->string('manager_name')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'is_active']);
            $table->index('type');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};

// update 87 

// u85

// u141

// uyjzcpwj
// pcz4owp7