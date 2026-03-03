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
        Schema::create('page_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('page_id')->constrained()->cascadeOnDelete();
            $table->uuid('parent_id')->nullable();
            $table->string('type');
            $table->json('content')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->unsignedTinyInteger('column_span')->default(12);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('page_blocks')
                ->nullOnDelete();

            $table->index(['page_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_blocks');
    }
};
