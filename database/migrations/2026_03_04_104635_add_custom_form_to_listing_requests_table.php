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
            $table->foreignUuid('custom_form_id')->nullable()->after('listing_id')->constrained('forms')->nullOnDelete();
            $table->timestamp('custom_form_completed_at')->nullable()->after('self_disclosure_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('custom_form_id');
            $table->dropColumn('custom_form_completed_at');
        });
    }
};
