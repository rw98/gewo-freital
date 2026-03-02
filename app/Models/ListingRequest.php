<?php

namespace App\Models;

use App\Enums\ListingRequestStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ListingRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ListingRequestFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'listing_id',
        'assigned_to',
        'approved_by',
        'email',
        'phone',
        'first_name',
        'middle_name',
        'last_name',
        'access_token',
        'status',
        'message',
        'requested_at',
        'email_confirmed_at',
        'approved_at',
        'signed_at',
        'closed_at',
        'rejected_at',
        'rejection_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ListingRequestStatus::class,
            'requested_at' => 'datetime',
            'email_confirmed_at' => 'datetime',
            'approved_at' => 'datetime',
            'signed_at' => 'datetime',
            'closed_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ListingRequest $request) {
            if (empty($request->access_token)) {
                $request->access_token = Str::random(64);
            }
            if (empty($request->requested_at)) {
                $request->requested_at = now();
            }
        });
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
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<RequestDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(RequestDocument::class);
    }

    /**
     * @return HasMany<RequestMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(RequestMessage::class);
    }

    /**
     * @return HasMany<RequestAppointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(RequestAppointment::class);
    }

    /**
     * Get the full name of the requestee.
     */
    public function fullName(): string
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);

        return implode(' ', $parts);
    }

    /**
     * Check if the request is in a terminal state.
     */
    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    /**
     * Check if the request is active.
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Check if the request can transition to the given status.
     */
    public function canTransitionTo(ListingRequestStatus $status): bool
    {
        return $this->status->canTransitionTo($status);
    }

    /**
     * Scope to get only active requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            ListingRequestStatus::Closed,
            ListingRequestStatus::Rejected,
        ]);
    }

    /**
     * Scope to get requests by status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeWithStatus($query, ListingRequestStatus $status)
    {
        return $query->where('status', $status);
    }
}
