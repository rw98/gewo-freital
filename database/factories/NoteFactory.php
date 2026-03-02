<?php

namespace Database\Factories;

use App\Models\Flat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flat_id' => Flat::factory(),
            'user_id' => User::factory(),
            'content' => fake()->paragraph(),
        ];
    }

    /**
     * Configure the model factory for a specific flat.
     */
    public function forFlat(Flat $flat): static
    {
        return $this->state(fn (array $attributes) => [
            'flat_id' => $flat->id,
        ]);
    }

    /**
     * Configure the model factory for a specific user.
     */
    public function byUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Configure the model factory without a user.
     */
    public function anonymous(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }
}
