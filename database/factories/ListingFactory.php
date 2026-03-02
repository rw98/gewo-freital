<?php

namespace Database\Factories;

use App\Models\Flat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rentCold = fake()->randomFloat(2, 400, 1500);
        $utilityCost = fake()->randomFloat(2, 80, 250);

        return [
            'flat_id' => Flat::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraphs(2, true),
            'status' => 'draft',
            'published_at' => null,
            'available_from' => fake()->optional()->dateTimeBetween('now', '+3 months'),
            'size_sqm' => fake()->randomFloat(2, 35, 120),
            'rent_cold' => $rentCold,
            'utility_cost' => $utilityCost,
            'floor' => fake()->numberBetween(0, 8),
            'flat_number' => fake()->numerify('##'),
            'rooms' => fake()->numberBetween(1, 5),
            'is_wheelchair_accessible' => fake()->boolean(20),
            'street' => fake()->streetName(),
            'street_number' => fake()->buildingNumber(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'has_elevator' => fake()->boolean(40),
            'year_built' => fake()->optional(0.8)->numberBetween(1950, 2020),
            'has_balcony' => fake()->boolean(60),
            'has_terrace' => fake()->boolean(20),
            'pets_allowed' => fake()->optional()->boolean(),
            'amenities' => fake()->optional()->randomElements([
                'Keller',
                'Stellplatz',
                'Einbauküche',
                'Fußbodenheizung',
                'Garten',
                'Waschküche',
            ], fake()->numberBetween(0, 4)),
        ];
    }

    /**
     * Set the listing as published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Set the listing as archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    /**
     * Create a listing from an existing flat.
     */
    public function fromFlat(Flat $flat): static
    {
        $rentalObject = $flat->rentalObject;

        return $this->state(fn (array $attributes) => [
            'flat_id' => $flat->id,
            'title' => "Wohnung {$flat->number} - {$rentalObject->street} {$rentalObject->number}",
            'size_sqm' => $flat->size_sqm,
            'rent_cold' => $flat->rent_cold,
            'utility_cost' => $flat->utility_cost,
            'floor' => $flat->floor,
            'flat_number' => $flat->number,
            'rooms' => $flat->rooms()->count(),
            'is_wheelchair_accessible' => $flat->is_wheelchair_accessible,
            'street' => $rentalObject->street,
            'street_number' => $rentalObject->number,
            'city' => $rentalObject->city,
            'postal_code' => $rentalObject->postal_code,
            'has_elevator' => $rentalObject->has_elevator,
            'year_built' => $rentalObject->year_built,
            'has_balcony' => $flat->outdoorSpaces()->where('type', 'balcony')->exists(),
            'has_terrace' => $flat->outdoorSpaces()->where('type', 'terrace')->exists(),
        ]);
    }
}
