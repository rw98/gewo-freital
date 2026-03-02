<?php

namespace App\Models;

use App\Enums\ImageType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'type',
        'path',
        'filename',
        'order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ImageType::class,
        ];
    }

    /**
     * Get the parent imageable model.
     *
     * @return MorphTo<Model, $this>
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the public URL for this image.
     */
    public function url(): string
    {
        return Storage::url($this->path);
    }
}
