<?php

namespace Database\Factories;

use App\Enums\BlockType;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageBlock>
 */
class PageBlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(BlockType::cases());

        return [
            'page_id' => Page::factory(),
            'parent_id' => null,
            'type' => $type,
            'content' => $type->defaultContent(),
            'settings' => $type->defaultSettings(),
            'order' => 0,
            'column_span' => 12,
        ];
    }

    /**
     * Set a specific block type.
     */
    public function ofType(BlockType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'content' => $type->defaultContent(),
        ]);
    }

    /**
     * Create a heading block.
     */
    public function heading(string $text = '', int $level = 2): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BlockType::Heading,
            'content' => ['text' => $text ?: fake()->sentence(4), 'level' => $level],
        ]);
    }

    /**
     * Create a paragraph block.
     */
    public function paragraph(string $text = ''): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BlockType::Paragraph,
            'content' => ['text' => $text ?: fake()->paragraph()],
        ]);
    }

    /**
     * Create a rich text block.
     */
    public function richText(string $html = ''): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BlockType::RichText,
            'content' => ['html' => $html ?: '<p>'.fake()->paragraphs(2, true).'</p>'],
        ]);
    }

    /**
     * Create an image block.
     */
    public function image(string $src = '', string $alt = ''): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BlockType::Image,
            'content' => [
                'src' => $src ?: 'https://via.placeholder.com/800x400',
                'alt' => $alt ?: fake()->sentence(3),
                'caption' => '',
            ],
        ]);
    }

    /**
     * Create a button block.
     */
    public function button(string $text = '', string $url = ''): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BlockType::Button,
            'content' => [
                'text' => $text ?: fake()->words(2, true),
                'url' => $url ?: '#',
                'variant' => 'primary',
                'size' => 'base',
            ],
        ]);
    }

    /**
     * Set the block as a child of another block.
     */
    public function childOf(string $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Set a specific order.
     */
    public function withOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
        ]);
    }

    /**
     * Set a specific column span.
     */
    public function withColumnSpan(int $span): static
    {
        return $this->state(fn (array $attributes) => [
            'column_span' => $span,
        ]);
    }
}
