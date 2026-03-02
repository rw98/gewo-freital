<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Flat extends Model
{
    /** @use HasFactory<\Database\Factories\FlatFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'rental_object_id',
        'size_sqm',
        'rent_cold',
        'utility_cost',
        'floor',
        'number',
        'description',
        'is_wheelchair_accessible',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size_sqm' => 'decimal:2',
            'rent_cold' => 'decimal:2',
            'utility_cost' => 'decimal:2',
            'is_wheelchair_accessible' => 'boolean',
        ];
    }

    /**
     * Get the rental object that owns this flat.
     *
     * @return BelongsTo<RentalObject, $this>
     */
    public function rentalObject(): BelongsTo
    {
        return $this->belongsTo(RentalObject::class);
    }

    /**
     * Get the rooms for this flat.
     *
     * @return HasMany<Room, $this>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get the tenants for this flat.
     *
     * @return BelongsToMany<User, $this>
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['move_in_date', 'move_out_date'])
            ->withTimestamps();
    }

    /**
     * Get the images for this flat.
     *
     * @return MorphMany<Image, $this>
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the notes for this flat.
     *
     * @return HasMany<Note, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get the outdoor spaces (balconies/terraces) for this flat.
     *
     * @return HasMany<OutdoorSpace, $this>
     */
    public function outdoorSpaces(): HasMany
    {
        return $this->hasMany(OutdoorSpace::class);
    }

    /**
     * Get the attributes for this flat.
     *
     * @return MorphMany<Attribute, $this>
     */
    public function attributes(): MorphMany
    {
        return $this->morphMany(Attribute::class, 'attributable');
    }

    /**
     * Get the listings for this flat.
     *
     * @return HasMany<Listing, $this>
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Get the total rent (cold + utilities).
     */
    public function totalRent(): float
    {
        return (float) $this->rent_cold + (float) $this->utility_cost;
    }
}
