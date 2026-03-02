<?php

namespace Database\Factories;

use App\Models\RentalObject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalObject>
 */
class RentalObjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'object_number' => fake()->unique()->numerify('OBJ-####'),
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => 'DE',
            'has_elevator' => fake()->boolean(30),
            'year_built' => fake()->optional(0.8)->numberBetween(1900, (int) date('Y')),
        ];
    }

    /**
     * Configure the model factory with an owner.
     */
    public function withOwner(User $user): static
    {
        return $this->afterCreating(function (RentalObject $rentalObject) use ($user) {
            $rentalObject->contacts()->attach($user, ['role' => 'owner']);
        });
    }

    /**
     * Configure the model factory with a manager.
     */
    public function withManager(User $user): static
    {
        return $this->afterCreating(function (RentalObject $rentalObject) use ($user) {
            $rentalObject->contacts()->attach($user, ['role' => 'manager']);
        });
    }

    /**
     * Configure the model factory with a caretaker.
     */
    public function withCaretaker(User $user): static
    {
        return $this->afterCreating(function (RentalObject $rentalObject) use ($user) {
            $rentalObject->contacts()->attach($user, ['role' => 'caretaker']);
        });
    }

    /**
     * Configure the model factory with an elevator.
     */
    public function withElevator(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_elevator' => true,
        ]);
    }
}
