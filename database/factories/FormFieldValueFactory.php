<?php

namespace Database\Factories;

use App\Models\FormField;
use App\Models\FormResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormFieldValue>
 */
class FormFieldValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_response_id' => FormResponse::factory(),
            'form_field_id' => FormField::factory(),
            'value' => fake()->sentence(),
            'file_path' => null,
            'file_name' => null,
        ];
    }

    /**
     * Set the value for a specific response.
     */
    public function forResponse(FormResponse $response): static
    {
        return $this->state(fn (array $attributes) => [
            'form_response_id' => $response->id,
        ]);
    }

    /**
     * Set the value for a specific field.
     */
    public function forField(FormField $field): static
    {
        return $this->state(fn (array $attributes) => [
            'form_field_id' => $field->id,
        ]);
    }

    /**
     * Set a file value.
     */
    public function withFile(string $path, string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => null,
            'file_path' => $path,
            'file_name' => $name,
        ]);
    }
}
