<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Add foreign key for current_route_id in driver_profiles
        Schema::table('driver_profiles', function (Blueprint $table) {
            $table->foreign('current_route_id')
                ->references('id')
                ->on('routes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('driver_profiles', function (Blueprint $table) {
            $table->dropForeign(['current_route_id']);
        });

        Schema::dropIfExists('personal_access_tokens');
    }
};

// update 57 

// update 112 

// update 264 

// update 369 

// update 402 

// u354

// u373

// e5bae3a