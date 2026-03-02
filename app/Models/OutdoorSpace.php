<?php

namespace App\Models;

use App\Enums\Orientation;
use App\Enums\OutdoorSpaceType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutdoorSpace extends Model
{
    /** @use HasFactory<\Database\Factories\OutdoorSpaceFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'flat_id',
        'type',
        'orientation',
        'size_sqm',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => OutdoorSpaceType::class,
            'orientation' => Orientation::class,
            'size_sqm' => 'decimal:2',
        ];
    }

    /**
     * Get the flat that owns this outdoor space.
     *
     * @return BelongsTo<Flat, $this>
     */
    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }
}
