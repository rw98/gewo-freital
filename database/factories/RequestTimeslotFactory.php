<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestTimeslot>
 */
class RequestTimeslotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+2 weeks');
        $endsAt = (clone $startsAt)->modify('+'.fake()->randomElement([30, 45, 60]).' minutes');

        return [
            'listing_id' => Listing::factory(),
            'created_by' => User::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'max_attendees' => fake()->numberBetween(1, 5),
            'location' => fake()->optional(0.3)->address(),
            'notes' => fake()->optional(0.2)->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Set the timeslot as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the timeslot in the past.
     */
    public function past(): static
    {
        $startsAt = fake()->dateTimeBetween('-2 weeks', '-1 day');
        $endsAt = (clone $startsAt)->modify('+30 minutes');

        return $this->state(fn (array $attributes) => [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);
    }

    /**
     * Set a specific start and end time.
     */
    public function at(\DateTimeInterface $startsAt, \DateTimeInterface $endsAt): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);
    }
}
