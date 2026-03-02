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
        Schema::create('outdoor_spaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('flat_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // balcony, terrace
            $table->string('orientation')->nullable(); // N, NE, E, SE, S, SW, W, NW
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outdoor_spaces');
    }
};
