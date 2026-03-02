<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestTimeslot extends Model
{
    /** @use HasFactory<\Database\Factories\RequestTimeslotFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'listing_id',
        'created_by',
        'starts_at',
        'ends_at',
        'max_attendees',
        'location',
        'notes',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'max_attendees' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Listing, $this>
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<RequestAppointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(RequestAppointment::class, 'timeslot_id');
    }

    /**
     * Get the number of remaining slots.
     */
    public function remainingSlots(): int
    {
        $booked = $this->appointments()
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return max(0, $this->max_attendees - $booked);
    }

    /**
     * Check if the timeslot has available slots.
     */
    public function hasAvailableSlots(): bool
    {
        return $this->remainingSlots() > 0;
    }

    /**
     * Check if the timeslot is bookable (active, in future, has slots).
     */
    public function isBookable(): bool
    {
        return $this->is_active
            && $this->starts_at->isFuture()
            && $this->hasAvailableSlots();
    }

    /**
     * Get the duration in minutes.
     */
    public function durationInMinutes(): int
    {
        return $this->starts_at->diffInMinutes($this->ends_at);
    }

    /**
     * Scope to get only active timeslots.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only future timeslots.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now());
    }

    /**
     * Scope to get bookable timeslots.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeBookable($query)
    {
        return $query->active()->upcoming();
    }
}
