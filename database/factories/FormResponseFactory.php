<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\ListingRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormResponse>
 */
class FormResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'listing_request_id' => null,
            'submitter_email' => fake()->email(),
            'submitter_name' => fake()->name(),
            'ip_address' => fake()->ipv4(),
            'submitted_at' => now(),
        ];
    }

    /**
     * Set the response for a specific form.
     */
    public function forForm(Form $form): static
    {
        return $this->state(fn (array $attributes) => [
            'form_id' => $form->id,
        ]);
    }

    /**
     * Set the response for a specific listing request.
     */
    public function forListingRequest(ListingRequest $listingRequest): static
    {
        return $this->state(fn (array $attributes) => [
            'listing_request_id' => $listingRequest->id,
        ]);
    }
}
