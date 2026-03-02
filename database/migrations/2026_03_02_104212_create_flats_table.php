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
        Schema::create('flats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rental_object_id')->constrained()->cascadeOnDelete();
            $table->decimal('size_sqm', 8, 2);
            $table->decimal('rent_cold', 10, 2);
            $table->decimal('utility_cost', 10, 2);
            $table->integer('floor');
            $table->string('number');
            $table->text('description')->nullable();
            $table->boolean('is_wheelchair_accessible')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flats');
    }
};
