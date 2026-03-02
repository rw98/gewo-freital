<?php

namespace Database\Factories;

use App\Enums\EnergyCertificateType;
use App\Enums\EnergySource;
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

    /**
     * Configure the model factory with energy certificate data.
     */
    public function withEnergyCertificate(?float $kwh = null): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_certificate_type' => fake()->randomElement(EnergyCertificateType::cases()),
            'energy_consumption_kwh' => $kwh ?? fake()->randomFloat(2, 30, 300),
            'energy_source' => fake()->randomElement(EnergySource::cases()),
            'energy_certificate_valid_until' => fake()->dateTimeBetween('now', '+10 years'),
        ]);
    }

    /**
     * Configure the model factory with a specific energy efficiency class.
     */
    public function withEnergyClass(string $class): static
    {
        $ranges = [
            'A+' => [0, 30],
            'A' => [30, 50],
            'B' => [50, 75],
            'C' => [75, 100],
            'D' => [100, 130],
            'E' => [130, 160],
            'F' => [160, 200],
            'G' => [200, 250],
            'H' => [250, 350],
        ];

        $range = $ranges[$class] ?? [100, 150];

        return $this->withEnergyCertificate(fake()->randomFloat(2, $range[0], $range[1]));
    }
}
