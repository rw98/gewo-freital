<?php

namespace App\Models;

use App\Enums\ListingStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Listing extends Model
{
    /** @use HasFactory<\Database\Factories\ListingFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'flat_id',
        'created_by',
        'title',
        'description',
        'status',
        'published_at',
        'available_from',
        'size_sqm',
        'rent_cold',
        'utility_cost',
        'floor',
        'flat_number',
        'rooms',
        'is_wheelchair_accessible',
        'street',
        'street_number',
        'city',
        'postal_code',
        'has_elevator',
        'year_built',
        'has_balcony',
        'has_terrace',
        'pets_allowed',
        'amenities',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ListingStatus::class,
            'published_at' => 'datetime',
            'available_from' => 'date',
            'size_sqm' => 'decimal:2',
            'rent_cold' => 'decimal:2',
            'utility_cost' => 'decimal:2',
            'is_wheelchair_accessible' => 'boolean',
            'has_elevator' => 'boolean',
            'has_balcony' => 'boolean',
            'has_terrace' => 'boolean',
            'pets_allowed' => 'boolean',
            'amenities' => 'array',
            'year_built' => 'integer',
        ];
    }

    /**
     * Get the flat this listing is for.
     *
     * @return BelongsTo<Flat, $this>
     */
    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }

    /**
     * Get the user who created this listing.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the images for this listing.
     *
     * @return MorphMany<Image, $this>
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the requests for this listing.
     *
     * @return HasMany<ListingRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(ListingRequest::class);
    }

    /**
     * Get the active requests for this listing.
     *
     * @return HasMany<ListingRequest, $this>
     */
    public function activeRequests(): HasMany
    {
        return $this->requests()->active();
    }

    /**
     * Get the timeslots for this listing.
     *
     * @return HasMany<RequestTimeslot, $this>
     */
    public function timeslots(): HasMany
    {
        return $this->hasMany(RequestTimeslot::class);
    }

    /**
     * Get the distribution channels for this listing.
     *
     * @return BelongsToMany<ListingDistribution, $this>
     */
    public function distributions(): BelongsToMany
    {
        return $this->belongsToMany(ListingDistribution::class)
            ->withPivot(['external_id', 'last_synced_at', 'sync_error'])
            ->withTimestamps();
    }

    /**
     * Get the total rent (cold + utilities).
     */
    public function totalRent(): float
    {
        return (float) $this->rent_cold + (float) $this->utility_cost;
    }

    /**
     * Get the full address as a string.
     */
    public function fullAddress(): string
    {
        return "{$this->street} {$this->street_number}, {$this->postal_code} {$this->city}";
    }

    /**
     * Check if the listing is published.
     */
    public function isPublished(): bool
    {
        return $this->status === ListingStatus::Published && $this->published_at !== null;
    }

    /**
     * Create a listing from a flat.
     *
     * @param  array<string, mixed>  $additional
     */
    public static function createFromFlat(Flat $flat, array $additional = []): self
    {
        $rentalObject = $flat->rentalObject;

        return self::create([
            'flat_id' => $flat->id,
            'title' => "Wohnung {$flat->number} - {$rentalObject->street} {$rentalObject->number}",
            'size_sqm' => $flat->size_sqm,
            'rent_cold' => $flat->rent_cold,
            'utility_cost' => $flat->utility_cost,
            'floor' => $flat->floor,
            'flat_number' => $flat->number,
            'rooms' => $flat->rooms()->count(),
            'is_wheelchair_accessible' => $flat->is_wheelchair_accessible,
            'street' => $rentalObject->street,
            'street_number' => $rentalObject->number,
            'city' => $rentalObject->city,
            'postal_code' => $rentalObject->postal_code,
            'has_elevator' => $rentalObject->has_elevator,
            'year_built' => $rentalObject->year_built,
            'has_balcony' => $flat->outdoorSpaces()->where('type', 'balcony')->exists(),
            'has_terrace' => $flat->outdoorSpaces()->where('type', 'terrace')->exists(),
            ...$additional,
        ]);
    }

    /**
     * Scope to get only published listings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopePublished($query)
    {
        return $query->where('status', ListingStatus::Published)
            ->whereNotNull('published_at');
    }

    /**
     * Scope to get only draft listings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeDraft($query)
    {
        return $query->where('status', ListingStatus::Draft);
    }
}
