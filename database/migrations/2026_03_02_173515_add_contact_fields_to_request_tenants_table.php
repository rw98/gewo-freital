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
        // Add contact fields to request_tenants
        Schema::table('request_tenants', function (Blueprint $table) {
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('email');
        });

        // Remove redundant personal info from listing_requests (now captured via tenants)
        Schema::table('listing_requests', function (Blueprint $table) {
            $table->dropColumn(['date_of_birth', 'nationality', 'current_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_tenants', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone']);
        });

        Schema::table('listing_requests', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('current_address')->nullable();
        });
    }
};
