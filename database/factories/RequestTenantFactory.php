<?php

namespace Database\Factories;

use App\Enums\EmploymentStatus;
use App\Models\ListingRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestTenant>
 */
class RequestTenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paysRent = fake()->boolean(70);

        return [
            'listing_request_id' => ListingRequest::factory(),
            'pays_rent' => $paysRent,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => $paysRent ? fake()->unique()->safeEmail() : null,
            'phone' => $paysRent ? fake()->optional(0.7)->phoneNumber() : null,
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'relationship' => fake()->optional(0.5)->randomElement(['Partner/in', 'Kind', 'WG-Mitbewohner', 'Ehepartner']),
            'employment_status' => $paysRent ? fake()->randomElement(EmploymentStatus::cases())->value : null,
            'monthly_net_income' => $paysRent ? fake()->randomFloat(2, 1000, 6000) : null,
        ];
    }

    /**
     * Indicate that the tenant pays rent.
     */
    public function paysRent(): static
    {
        return $this->state(fn (array $attributes) => [
            'pays_rent' => true,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'employment_status' => fake()->randomElement(EmploymentStatus::cases())->value,
            'monthly_net_income' => fake()->randomFloat(2, 1500, 5000),
        ]);
    }

    /**
     * Indicate that the tenant is an occupant only (doesn't pay rent).
     */
    public function occupant(): static
    {
        return $this->state(fn (array $attributes) => [
            'pays_rent' => false,
            'email' => null,
            'phone' => null,
            'employment_status' => null,
            'monthly_net_income' => null,
        ]);
    }

    /**
     * Indicate that the tenant is a child.
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'pays_rent' => false,
            'date_of_birth' => fake()->dateTimeBetween('-17 years', '-1 year')->format('Y-m-d'),
            'relationship' => 'Kind',
            'employment_status' => null,
            'monthly_net_income' => null,
        ]);
    }
}
