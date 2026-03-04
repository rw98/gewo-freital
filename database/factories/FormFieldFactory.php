<?php

namespace Database\Factories;

use App\Enums\FormFieldType;
use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormField>
 */
class FormFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(FormFieldType::cases());
        $label = fake()->words(2, true);

        return [
            'form_id' => Form::factory(),
            'type' => $type,
            'name' => Str::snake($label),
            'label' => ucfirst($label),
            'description' => fake()->optional()->sentence(),
            'placeholder' => fake()->optional()->word(),
            'config' => $type->defaultConfig(),
            'validation_rules' => [],
            'order' => 0,
            'is_required' => fake()->boolean(70),
        ];
    }

    /**
     * Set the field type.
     */
    public function type(FormFieldType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'config' => $type->defaultConfig(),
        ]);
    }

    /**
     * Set the field as required.
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }

    /**
     * Set the field as optional.
     */
    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => false,
        ]);
    }

    /**
     * Set the field for a specific form.
     */
    public function forForm(Form $form): static
    {
        return $this->state(fn (array $attributes) => [
            'form_id' => $form->id,
        ]);
    }
}
