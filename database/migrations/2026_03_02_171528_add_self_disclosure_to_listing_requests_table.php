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
            // Self-disclosure (Selbstauskunft) fields
            $table->date('date_of_birth')->nullable()->after('last_name');
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->string('current_address')->nullable()->after('nationality');

            // Employment
            $table->string('employment_status')->nullable()->after('current_address');
            $table->string('employer_name')->nullable()->after('employment_status');
            $table->string('employer_address')->nullable()->after('employer_name');
            $table->date('employed_since')->nullable()->after('employer_address');
            $table->decimal('monthly_net_income', 10, 2)->nullable()->after('employed_since');

            // Household
            $table->boolean('has_pets')->nullable()->after('monthly_net_income');
            $table->string('pets_details')->nullable()->after('has_pets');
            $table->boolean('is_smoker')->nullable()->after('pets_details');

            // Current tenancy
            $table->string('current_landlord_name')->nullable()->after('is_smoker');
            $table->string('current_landlord_phone')->nullable()->after('current_landlord_name');
            $table->string('current_landlord_email')->nullable()->after('current_landlord_phone');
            $table->text('reason_for_moving')->nullable()->after('current_landlord_email');
            $table->date('desired_move_in_date')->nullable()->after('reason_for_moving');

            // Financial
            $table->boolean('has_insolvency')->nullable()->after('desired_move_in_date');
            $table->boolean('has_eviction_history')->nullable()->after('has_insolvency');
            $table->boolean('has_rental_debt')->nullable()->after('has_eviction_history');
            $table->text('additional_notes')->nullable()->after('has_rental_debt');

            // Tracking
            $table->timestamp('self_disclosure_completed_at')->nullable()->after('additional_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_requests', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'nationality',
                'current_address',
                'employment_status',
                'employer_name',
                'employer_address',
                'employed_since',
                'monthly_net_income',
                'has_pets',
                'pets_details',
                'is_smoker',
                'current_landlord_name',
                'current_landlord_phone',
                'current_landlord_email',
                'reason_for_moving',
                'desired_move_in_date',
                'has_insolvency',
                'has_eviction_history',
                'has_rental_debt',
                'additional_notes',
                'self_disclosure_completed_at',
            ]);
        });
    }
};
