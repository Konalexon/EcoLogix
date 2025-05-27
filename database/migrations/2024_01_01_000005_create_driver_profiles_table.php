<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // License info
            $table->string('license_number', 20)->unique();
            $table->json('license_categories'); // ["B", "C", "CE"]
            $table->date('license_issue_date');
            $table->date('license_expiry_date');
            $table->string('license_issuing_authority')->nullable();

            // Medical
            $table->date('medical_certificate_expiry');
            $table->date('psychotechnical_test_expiry')->nullable();

            // Employment
            $table->date('employment_date');
            $table->enum('employment_type', ['full_time', 'part_time', 'contractor'])->default('full_time');

            // Status
            $table->string('availability')->default('available'); // available, on_route, on_break, off_duty, vacation, sick_leave
            $table->timestamp('availability_changed_at')->nullable();

            // Current assignment
            $table->foreignId('current_vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('current_route_id')->nullable();

            // Statistics
            $table->integer('total_deliveries')->default(0);
            $table->integer('successful_deliveries')->default(0);
            $table->integer('failed_deliveries')->default(0);
            $table->decimal('total_distance_km', 12, 2)->default(0);
            $table->integer('total_driving_minutes')->default(0);
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->integer('rating_count')->default(0);

            // Preferences
            $table->integer('preferred_max_packages_per_day')->nullable();
            $table->json('preferred_areas')->nullable(); // preferred delivery zones

            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relation')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('availability');
            $table->index('license_expiry_date');
            $table->index('medical_certificate_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_profiles');
    }
};

// update 106 

// update 210 

// update 304 

// update 412 

// cw2q577
// hnghgbgp