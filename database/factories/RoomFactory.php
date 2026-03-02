<?php

namespace Database\Factories;

use App\Models\Flat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
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
            'name' => fake()->randomElement([
                'Living Room',
                'Bedroom',
                'Kitchen',
                'Bathroom',
                'Office',
                'Guest Room',
                'Storage',
            ]),
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
}
