<?php

namespace Database\Factories;

use App\Models\Flat;
use App\Models\RentalObject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flat>
 */
class FlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_object_id' => RentalObject::factory(),
            'size_sqm' => fake()->randomFloat(2, 30, 150),
            'rent_cold' => fake()->randomFloat(2, 400, 2000),
            'utility_cost' => fake()->randomFloat(2, 50, 300),
            'floor' => fake()->numberBetween(0, 10),
            'number' => fake()->numerify('##'),
            'description' => fake()->optional()->paragraph(),
            'is_wheelchair_accessible' => fake()->boolean(20),
        ];
    }

    /**
     * Configure the model factory for a specific rental object.
     */
    public function forRentalObject(RentalObject $rentalObject): static
    {
        return $this->state(fn (array $attributes) => [
            'rental_object_id' => $rentalObject->id,
        ]);
    }

    /**
     * Configure the model factory as wheelchair accessible.
     */
    public function wheelchairAccessible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_wheelchair_accessible' => true,
        ]);
    }

    /**
     * Configure the model factory with a tenant.
     */
    public function withTenant(User $user, ?string $moveInDate = null): static
    {
        return $this->afterCreating(function (Flat $flat) use ($user, $moveInDate) {
            $flat->tenants()->attach($user, [
                'move_in_date' => $moveInDate ?? now()->subMonths(6)->toDateString(),
            ]);
        });
    }
}
