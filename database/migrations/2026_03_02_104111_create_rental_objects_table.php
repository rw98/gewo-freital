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
        Schema::create('rental_objects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('street');
            $table->string('number');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country')->default('DE');
            $table->boolean('has_elevator')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_objects');
    }
};
