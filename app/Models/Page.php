<?php

namespace App\Models;

use App\Enums\PageLayout;
use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'slug',
        'meta_description',
        'meta_keywords',
        'status',
        'layout',
        'created_by',
        'updated_by',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
            'layout' => PageLayout::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the blocks for this page.
     *
     * @return HasMany<PageBlock, $this>
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->whereNull('parent_id')->orderBy('order');
    }

    /**
     * Get all blocks including nested ones.
     *
     * @return HasMany<PageBlock, $this>
     */
    public function allBlocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('order');
    }

    /**
     * Get the user who created this page.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this page.
     *
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if the page is published.
     */
    public function isPublished(): bool
    {
        return $this->status === PageStatus::Published && $this->published_at !== null;
    }

    /**
     * Check if the page is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === PageStatus::Draft;
    }

    /**
     * Check if the page is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === PageStatus::Archived;
    }

    /**
     * Publish the page.
     */
    public function publish(): void
    {
        $this->update([
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish the page (set to draft).
     */
    public function unpublish(): void
    {
        $this->update([
            'status' => PageStatus::Draft,
        ]);
    }

    /**
     * Archive the page.
     */
    public function archive(): void
    {
        $this->update([
            'status' => PageStatus::Archived,
        ]);
    }
}
