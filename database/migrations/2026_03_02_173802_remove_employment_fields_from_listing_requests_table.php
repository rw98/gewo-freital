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
        Schema::table('listing_requests', function (Blueprint $table) {
            $table->dropColumn(['employment_status', 'job_title', 'monthly_net_income']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_requests', function (Blueprint $table) {
            $table->string('employment_status')->nullable();
            $table->string('job_title')->nullable();
            $table->decimal('monthly_net_income', 10, 2)->nullable();
        });
    }
};
