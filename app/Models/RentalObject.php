<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RentalObject extends Model
{
    /** @use HasFactory<\Database\Factories\RentalObjectFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'object_number',
        'street',
        'number',
        'city',
        'postal_code',
        'country',
        'has_elevator',
        'year_built',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_elevator' => 'boolean',
            'year_built' => 'integer',
        ];
    }

    /**
     * Get the contacts (users) for this rental object.
     *
     * @return BelongsToMany<User, $this>
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the flats for this rental object.
     *
     * @return HasMany<Flat, $this>
     */
    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class);
    }

    /**
     * Get the images for this rental object.
     *
     * @return MorphMany<Image, $this>
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the attributes for this rental object.
     *
     * @return MorphMany<Attribute, $this>
     */
    public function attributes(): MorphMany
    {
        return $this->morphMany(Attribute::class, 'attributable');
    }

    /**
     * Get the full address as a string.
     */
    public function fullAddress(): string
    {
        return "{$this->street} {$this->number}, {$this->postal_code} {$this->city}";
    }
}
