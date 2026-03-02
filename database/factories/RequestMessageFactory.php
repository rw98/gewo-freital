<?php

namespace Database\Factories;

use App\Models\ListingRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestMessage>
 */
class RequestMessageFactory extends Factory
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
            'user_id' => null,
            'sender_type' => 'requestee',
            'content' => fake()->paragraph(),
            'read_at' => null,
        ];
    }

    /**
     * Set the message as sent by an employee.
     */
    public function fromEmployee(?User $user = null): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_type' => 'employee',
            'user_id' => $user?->id ?? User::factory(),
        ]);
    }

    /**
     * Set the message as read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }
}
