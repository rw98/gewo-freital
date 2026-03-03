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
        Schema::create('request_tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('listing_request_id')->constrained()->cascadeOnDelete();
            $table->boolean('pays_rent')->default(true); // true = paying tenant (needs full info), false = occupant only (basic info)
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('relationship')->nullable(); // e.g., spouse, child, partner
            // Only required for paying tenants
            $table->string('employment_status')->nullable();
            $table->decimal('monthly_net_income', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_tenants');
    }
};
