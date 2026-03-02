<?php

namespace Database\Factories;

use App\Enums\RequestAppointmentStatus;
use App\Models\ListingRequest;
use App\Models\RequestTimeslot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestAppointment>
 */
class RequestAppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listing_request_id' => ListingRequest::factory(),
            'timeslot_id' => RequestTimeslot::factory(),
            'status' => RequestAppointmentStatus::Pending,
            'confirmed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }

    /**
     * Set the appointment as confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RequestAppointmentStatus::Confirmed,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Set the appointment as cancelled.
     */
    public function cancelled(?string $reason = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RequestAppointmentStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason ?? fake()->sentence(),
        ]);
    }

    /**
     * Set the appointment as completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RequestAppointmentStatus::Completed,
            'confirmed_at' => now()->subDay(),
        ]);
    }
}
