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
        Schema::create('listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('flat_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Listing metadata
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamp('published_at')->nullable();
            $table->date('available_from')->nullable();

            // Copied flat information (denormalized)
            $table->decimal('size_sqm', 8, 2);
            $table->decimal('rent_cold', 10, 2);
            $table->decimal('utility_cost', 10, 2);
            $table->integer('floor');
            $table->string('flat_number');
            $table->integer('rooms')->nullable();
            $table->boolean('is_wheelchair_accessible')->default(false);

            // Copied property information (denormalized)
            $table->string('street');
            $table->string('street_number');
            $table->string('city');
            $table->string('postal_code');
            $table->boolean('has_elevator')->default(false);
            $table->unsignedSmallInteger('year_built')->nullable();

            // Additional listing features
            $table->boolean('has_balcony')->default(false);
            $table->boolean('has_terrace')->default(false);
            $table->boolean('pets_allowed')->nullable();
            $table->text('amenities')->nullable(); // JSON array of amenities

            $table->timestamps();

            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
