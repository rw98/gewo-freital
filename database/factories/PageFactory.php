<?php

namespace Database\Factories;

use App\Enums\PageLayout;
use App\Enums\PageStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'meta_description' => fake()->optional()->sentence(),
            'meta_keywords' => fake()->optional()->words(5, true),
            'status' => PageStatus::Draft,
            'layout' => PageLayout::Default,
            'created_by' => User::factory(),
            'updated_by' => null,
            'published_at' => null,
        ];
    }

    /**
     * Set the page as published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);
    }

    /**
     * Set the page as archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PageStatus::Archived,
        ]);
    }

    /**
     * Set a specific layout.
     */
    public function withLayout(PageLayout $layout): static
    {
        return $this->state(fn (array $attributes) => [
            'layout' => $layout,
        ]);
    }

    /**
     * Set the page for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }
}
