<?php

namespace Database\Factories;

use App\Enums\Orientation;
use App\Enums\OutdoorSpaceType;
use App\Models\Flat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OutdoorSpace>
 */
class OutdoorSpaceFactory extends Factory
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
            'type' => fake()->randomElement(OutdoorSpaceType::cases()),
            'orientation' => fake()->randomElement(Orientation::cases()),
            'size_sqm' => fake()->randomFloat(2, 3, 30),
        ];
    }

    /**
     * Configure the outdoor space as a balcony.
     */
    public function balcony(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => OutdoorSpaceType::Balcony,
        ]);
    }

    /**
     * Configure the outdoor space as a terrace.
     */
    public function terrace(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => OutdoorSpaceType::Terrace,
        ]);
    }
}
