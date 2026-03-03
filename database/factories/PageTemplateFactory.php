<?php

namespace Database\Factories;

use App\Enums\BlockType;
use App\Enums\TemplateCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageTemplate>
 */
class PageTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(TemplateCategory::cases()),
            'structure' => $this->defaultStructure(),
            'thumbnail_path' => null,
            'is_active' => true,
        ];
    }

    /**
     * Create a landing page template.
     */
    public function landing(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Landing Page',
            'slug' => 'landing-page',
            'category' => TemplateCategory::Landing,
            'structure' => [
                ['type' => BlockType::Hero->value, 'content' => BlockType::Hero->defaultContent()],
                ['type' => BlockType::FeatureGrid->value, 'content' => BlockType::FeatureGrid->defaultContent()],
                ['type' => BlockType::Testimonials->value, 'content' => BlockType::Testimonials->defaultContent()],
                ['type' => BlockType::Cta->value, 'content' => BlockType::Cta->defaultContent()],
            ],
        ]);
    }

    /**
     * Create a contact page template.
     */
    public function contact(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Contact Page',
            'slug' => 'contact-page',
            'category' => TemplateCategory::Contact,
            'structure' => [
                ['type' => BlockType::Heading->value, 'content' => ['text' => 'Kontakt', 'level' => 1]],
                ['type' => BlockType::Paragraph->value, 'content' => ['text' => '']],
                ['type' => BlockType::ContactForm->value, 'content' => BlockType::ContactForm->defaultContent()],
            ],
        ]);
    }

    /**
     * Set a specific category.
     */
    public function withCategory(TemplateCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Set template as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function defaultStructure(): array
    {
        return [
            ['type' => BlockType::Heading->value, 'content' => ['text' => '', 'level' => 1]],
            ['type' => BlockType::Paragraph->value, 'content' => ['text' => '']],
        ];
    }
}
