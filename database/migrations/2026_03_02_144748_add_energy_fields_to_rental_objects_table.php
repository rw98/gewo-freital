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
        Schema::table('rental_objects', function (Blueprint $table) {
            $table->string('energy_certificate_type')->nullable()->after('year_built');
            $table->decimal('energy_consumption_kwh', 6, 2)->nullable()->after('energy_certificate_type');
            $table->string('energy_source')->nullable()->after('energy_consumption_kwh');
            $table->date('energy_certificate_valid_until')->nullable()->after('energy_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_objects', function (Blueprint $table) {
            $table->dropColumn([
                'energy_certificate_type',
                'energy_consumption_kwh',
                'energy_source',
                'energy_certificate_valid_until',
            ]);
        });
    }
};
