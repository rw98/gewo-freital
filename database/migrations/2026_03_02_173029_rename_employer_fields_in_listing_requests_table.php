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
            $table->dropColumn(['employer_address', 'employed_since']);
            $table->renameColumn('employer_name', 'job_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_requests', function (Blueprint $table) {
            $table->renameColumn('job_title', 'employer_name');
            $table->string('employer_address')->nullable()->after('employer_name');
            $table->date('employed_since')->nullable()->after('employer_address');
        });
    }
};
