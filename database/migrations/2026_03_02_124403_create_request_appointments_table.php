<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('listing_request_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('timeslot_id')->constrained('request_timeslots')->cascadeOnDelete();

            $table->string('status')->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_appointments');
    }
};
