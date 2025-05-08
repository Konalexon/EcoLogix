<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->uuid('tracking_uuid')->unique();
            $table->string('tracking_number', 20)->unique();
            $table->string('barcode')->unique()->nullable();

            // Relations
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('current_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('current_route_id')->nullable()->constrained('routes')->nullOnDelete();
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->nullOnDelete();

            // Status and priority
            $table->string('status')->default('pending');
            $table->string('priority')->default('standard'); // standard, express, overnight, same_day

            // Sender info
            $table->string('sender_name');
            $table->string('sender_company')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_phone', 20);
            $table->string('sender_street');
            $table->string('sender_building', 10);
            $table->string('sender_apartment', 10)->nullable();
            $table->string('sender_city');
            $table->string('sender_postal_code', 10);
            $table->string('sender_country', 2)->default('PL');
            $table->decimal('sender_latitude', 10, 8)->nullable();
            $table->decimal('sender_longitude', 11, 8)->nullable();

            // Recipient info
            $table->string('recipient_name');
            $table->string('recipient_company')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone', 20);
            $table->string('recipient_street');
            $table->string('recipient_building', 10);
            $table->string('recipient_apartment', 10)->nullable();
            $table->string('recipient_city');
            $table->string('recipient_postal_code', 10);
            $table->string('recipient_country', 2)->default('PL');
            $table->decimal('recipient_latitude', 10, 8)->nullable();
            $table->decimal('recipient_longitude', 11, 8)->nullable();

            // Dimensions
            $table->decimal('weight_kg', 8, 2);
            $table->decimal('length_cm', 6, 2);
            $table->decimal('width_cm', 6, 2);
            $table->decimal('height_cm', 6, 2);
            $table->decimal('volume_m3', 10, 6)->storedAs('(length_cm * width_cm * height_cm) / 1000000');
            $table->decimal('volumetric_weight_kg', 8, 2)->storedAs('(length_cm * width_cm * height_cm) / 5000');
            $table->decimal('chargeable_weight_kg', 8, 2)->storedAs('GREATEST(weight_kg, (length_cm * width_cm * height_cm) / 5000)');

            // Package details
            $table->integer('pieces_count')->default(1);
            $table->string('package_type')->default('box'); // box, envelope, pallet, tube, custom
            $table->text('contents_description')->nullable();

            // Special handling
            $table->boolean('is_fragile')->default(false);
            $table->boolean('requires_signature')->default(false);
            $table->boolean('is_hazardous')->default(false);
            $table->string('hazmat_class')->nullable();
            $table->boolean('requires_refrigeration')->default(false);
            $table->decimal('min_temperature', 4, 1)->nullable();
            $table->decimal('max_temperature', 4, 1)->nullable();
            $table->boolean('is_oversized')->default(false);
            $table->boolean('keep_upright')->default(false);

            // Value and insurance
            $table->decimal('declared_value', 12, 2)->nullable();
            $table->string('currency', 3)->default('PLN');
            $table->boolean('is_insured')->default(false);
            $table->decimal('insurance_value', 12, 2)->nullable();

            // COD
            $table->boolean('is_cod')->default(false);
            $table->decimal('cod_amount', 10, 2)->nullable();
            $table->boolean('cod_collected')->default(false);

            // Timestamps
            $table->timestamp('estimated_pickup_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('first_delivery_attempt_at')->nullable();

            // Delivery info
            $table->integer('delivery_attempts')->default(0);
            $table->integer('max_delivery_attempts')->default(3);
            $table->string('delivery_window_start', 5)->nullable(); // HH:MM
            $table->string('delivery_window_end', 5)->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->string('safe_place')->nullable(); // Where to leave if not home

            // Proof of delivery
            $table->string('recipient_signature_path')->nullable();
            $table->string('delivery_photo_path')->nullable();
            $table->string('received_by_name')->nullable();
            $table->string('received_by_relation')->nullable(); // recipient, neighbor, security, etc.

            // Failure info
            $table->string('failure_reason')->nullable();
            $table->text('failure_notes')->nullable();

            // Pricing
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('fuel_surcharge', 10, 2)->nullable();
            $table->decimal('insurance_fee', 10, 2)->nullable();
            $table->decimal('special_handling_fee', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();

            // Documents
            $table->string('waybill_path')->nullable();
            $table->string('label_path')->nullable();

            // References
            $table->string('customer_reference')->nullable();
            $table->string('external_reference')->nullable();

            // Notes
            $table->text('internal_notes')->nullable();
            $table->text('special_instructions')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index('current_warehouse_id');
            $table->index('current_route_id');
            $table->index('assigned_driver_id');
            $table->index('estimated_delivery_at');
            $table->index('recipient_city');
            $table->index('sender_city');
            $table->index('created_at');
            $table->index(['recipient_latitude', 'recipient_longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

// update 54 

// update 55 

// update 69 

// u120

// u122

// u158

// u165

// u266

// u308

// u405

// c0o513c9
// ut7xga3k