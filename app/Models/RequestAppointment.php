<?php

namespace App\Models;

use App\Enums\RequestAppointmentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestAppointment extends Model
{
    /** @use HasFactory<\Database\Factories\RequestAppointmentFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'listing_request_id',
        'timeslot_id',
        'status',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RequestAppointmentStatus::class,
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ListingRequest, $this>
     */
    public function listingRequest(): BelongsTo
    {
        return $this->belongsTo(ListingRequest::class);
    }

    /**
     * @return BelongsTo<RequestTimeslot, $this>
     */
    public function timeslot(): BelongsTo
    {
        return $this->belongsTo(RequestTimeslot::class, 'timeslot_id');
    }

    /**
     * Check if the appointment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === RequestAppointmentStatus::Pending;
    }

    /**
     * Check if the appointment is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === RequestAppointmentStatus::Confirmed;
    }

    /**
     * Check if the appointment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === RequestAppointmentStatus::Cancelled;
    }

    /**
     * Check if the appointment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === RequestAppointmentStatus::Completed;
    }

    /**
     * Confirm the appointment.
     */
    public function confirm(): void
    {
        $this->update([
            'status' => RequestAppointmentStatus::Confirmed,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Cancel the appointment.
     */
    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => RequestAppointmentStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Mark the appointment as completed.
     */
    public function complete(): void
    {
        $this->update([
            'status' => RequestAppointmentStatus::Completed,
        ]);
    }
}
