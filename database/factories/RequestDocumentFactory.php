<?php

namespace Database\Factories;

use App\Enums\RequestDocumentType;
use App\Models\ListingRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestDocument>
 */
class RequestDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->uuid().'.pdf';

        return [
            'listing_request_id' => ListingRequest::factory(),
            'uploaded_by_user_id' => null,
            'type' => fake()->randomElement(RequestDocumentType::cases()),
            'path' => 'request-documents/'.$filename,
            'filename' => $filename,
            'original_filename' => fake()->word().'.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => fake()->numberBetween(10000, 5000000),
            'uploaded_by' => 'requestee',
        ];
    }

    /**
     * Set the document as uploaded by an employee.
     */
    public function uploadedByEmployee(?User $user = null): static
    {
        return $this->state(fn (array $attributes) => [
            'uploaded_by' => 'employee',
            'uploaded_by_user_id' => $user?->id ?? User::factory(),
        ]);
    }

    /**
     * Set a specific document type.
     */
    public function type(RequestDocumentType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }
}
