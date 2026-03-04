<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listing_requests', function (Blueprint $table) {
            $table->json('form_prefilled_values')->nullable()->after('custom_form_id');
            $table->json('form_locked_fields')->nullable()->after('form_prefilled_values');
        });
    }

    public function down(): void
    {
        Schema::table('listing_requests', function (Blueprint $table) {
            $table->dropColumn(['form_prefilled_values', 'form_locked_fields']);
        });
    }
};
