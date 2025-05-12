<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('tax_id', 20)->unique();
            $table->string('regon', 14)->nullable();
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->string('website')->nullable();

            // Address
            $table->string('street');
            $table->string('building_number', 10);
            $table->string('apartment_number', 10)->nullable();
            $table->string('city');
            $table->string('postal_code', 10);
            $table->string('country', 2)->default('PL');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Type and status
            $table->string('type')->default('shipper'); // shipper, carrier, both
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            // Billing
            $table->string('billing_email')->nullable();
            $table->enum('payment_terms', ['prepaid', 'net_7', 'net_14', 'net_30'])->default('prepaid');
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->decimal('current_balance', 12, 2)->default(0);

            // Branding
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 7)->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'type']);
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

// update 220 

// update 266 

// u360

// cfl4h4