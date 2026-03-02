<?php

namespace Database\Factories;

use App\Models\RentalObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attributable_id' => RentalObject::factory(),
            'attributable_type' => RentalObject::class,
            'title' => fake()->randomElement([
                'Baujahr',
                'Heizungsart',
                'Energieausweis',
                'Denkmalschutz',
                'Renoviert',
                'Keller',
                'Dachboden',
                'Parkplätze',
            ]),
            'description' => fake()->optional()->sentence(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
