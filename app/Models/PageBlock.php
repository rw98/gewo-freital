<?php

namespace App\Models;

use App\Enums\BlockType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageBlock extends Model
{
    /** @use HasFactory<\Database\Factories\PageBlockFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'page_id',
        'parent_id',
        'type',
        'content',
        'settings',
        'order',
        'column_span',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => BlockType::class,
            'content' => 'array',
            'settings' => 'array',
            'order' => 'integer',
            'column_span' => 'integer',
        ];
    }

    /**
     * Get the page this block belongs to.
     *
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the parent block if this is a nested block.
     *
     * @return BelongsTo<PageBlock, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PageBlock::class, 'parent_id');
    }

    /**
     * Get the child blocks.
     *
     * @return HasMany<PageBlock, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(PageBlock::class, 'parent_id')->orderBy('order');
    }

    /**
     * Check if this block supports children.
     */
    public function supportsChildren(): bool
    {
        return $this->type->supportsChildren();
    }

    /**
     * Get a content value by key.
     */
    public function getContent(string $key, mixed $default = null): mixed
    {
        return data_get($this->content, $key, $default);
    }

    /**
     * Get a settings value by key.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Duplicate this block.
     */
    public function duplicate(): static
    {
        $clone = $this->replicate();
        $clone->order = $this->order + 1;
        $clone->save();

        // Duplicate children if any
        foreach ($this->children as $child) {
            $childClone = $child->replicate();
            $childClone->parent_id = $clone->id;
            $childClone->save();
        }

        return $clone;
    }
}
