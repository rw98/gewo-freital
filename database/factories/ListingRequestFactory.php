<?php

namespace Database\Factories;

use App\Enums\ListingRequestStatus;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingRequest>
 */
class ListingRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listing_id' => Listing::factory(),
            'assigned_to' => null,
            'approved_by' => null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional(0.2)->firstName(),
            'last_name' => fake()->lastName(),
            'access_token' => Str::random(64),
            'status' => ListingRequestStatus::Requested,
            'message' => fake()->optional()->paragraph(),
            'requested_at' => now(),
            'email_confirmed_at' => null,
            'approved_at' => null,
            'signed_at' => null,
            'closed_at' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ];
    }

    /**
     * Set the request as pending email confirmation.
     */
    public function pendingEmailConfirmation(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingRequestStatus::PendingEmailConfirmation,
        ]);
    }

    /**
     * Set the request as confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingRequestStatus::Confirmed,
            'email_confirmed_at' => now(),
        ]);
    }

    /**
     * Set the request as approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingRequestStatus::Approved,
            'email_confirmed_at' => now()->subDays(5),
            'approved_at' => now(),
            'approved_by' => User::factory(),
        ]);
    }

    /**
     * Set the request as rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingRequestStatus::Rejected,
            'email_confirmed_at' => now()->subDays(5),
            'rejected_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Set the request as signed.
     */
    public function signed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingRequestStatus::Signed,
            'email_confirmed_at' => now()->subDays(10),
            'approved_at' => now()->subDays(3),
            'approved_by' => User::factory(),
            'signed_at' => now(),
        ]);
    }

    /**
     * Set the request as closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingRequestStatus::Closed,
            'email_confirmed_at' => now()->subDays(14),
            'approved_at' => now()->subDays(7),
            'approved_by' => User::factory(),
            'signed_at' => now()->subDays(3),
            'closed_at' => now(),
        ]);
    }

    /**
     * Assign the request to a user.
     */
    public function assignedTo(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $user->id,
        ]);
    }
}
