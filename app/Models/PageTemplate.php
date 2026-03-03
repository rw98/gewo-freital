<?php

namespace App\Models;

use App\Enums\TemplateCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\PageTemplateFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'structure',
        'thumbnail_path',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => TemplateCategory::class,
            'structure' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope to only active templates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<PageTemplate>  $query
     * @return \Illuminate\Database\Eloquent\Builder<PageTemplate>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<PageTemplate>  $query
     * @return \Illuminate\Database\Eloquent\Builder<PageTemplate>
     */
    public function scopeCategory($query, TemplateCategory $category)
    {
        return $query->where('category', $category);
    }
}
