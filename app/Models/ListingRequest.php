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
        'custom_form_id',
        'form_prefilled_values',
        'form_locked_fields',
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
        // Self-disclosure fields
        'has_pets',
        'pets_details',
        'is_smoker',
        'current_landlord_name',
        'current_landlord_phone',
        'current_landlord_email',
        'reason_for_moving',
        'desired_move_in_date',
        'has_insolvency',
        'has_eviction_history',
        'has_rental_debt',
        'additional_notes',
        'self_disclosure_completed_at',
        'custom_form_completed_at',
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
            // Self-disclosure casts
            'has_pets' => 'boolean',
            'is_smoker' => 'boolean',
            'desired_move_in_date' => 'date',
            'has_insolvency' => 'boolean',
            'has_eviction_history' => 'boolean',
            'has_rental_debt' => 'boolean',
            'self_disclosure_completed_at' => 'datetime',
            'custom_form_completed_at' => 'datetime',
            'form_prefilled_values' => 'array',
            'form_locked_fields' => 'array',
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
     * @return HasMany<RequestTenant, $this>
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(RequestTenant::class);
    }

    /**
     * @return BelongsTo<Form, $this>
     */
    public function customForm(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'custom_form_id');
    }

    /**
     * @return HasMany<FormResponse, $this>
     */
    public function formResponses(): HasMany
    {
        return $this->hasMany(FormResponse::class);
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

    /**
     * Check if self-disclosure form can be filled.
     */
    public function canFillSelfDisclosure(): bool
    {
        return in_array($this->status, [
            ListingRequestStatus::AppointmentPending,
            ListingRequestStatus::WaitingForInformation,
            ListingRequestStatus::WaitingForApproval,
        ], true);
    }

    /**
     * Check if self-disclosure form has been completed.
     */
    public function hasSelfDisclosure(): bool
    {
        return $this->self_disclosure_completed_at !== null;
    }

    /**
     * Check if custom form can be filled.
     */
    public function canFillCustomForm(): bool
    {
        return $this->custom_form_id !== null && in_array($this->status, [
            ListingRequestStatus::AppointmentPending,
            ListingRequestStatus::WaitingForInformation,
            ListingRequestStatus::WaitingForApproval,
        ], true);
    }

    /**
     * Check if custom form has been completed.
     */
    public function hasCustomForm(): bool
    {
        return $this->custom_form_completed_at !== null;
    }
}
