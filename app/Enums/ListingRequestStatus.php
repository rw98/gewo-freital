<?php

namespace App\Enums;

enum ListingRequestStatus: string
{
    case Requested = 'requested';
    case PendingEmailConfirmation = 'pending_email_confirmation';
    case Confirmed = 'confirmed';
    case AppointmentPending = 'appointment_pending';
    case WaitingForInformation = 'waiting_for_information';
    case WaitingForApproval = 'waiting_for_approval';
    case Approved = 'approved';
    case WaitingForSignature = 'waiting_for_signature';
    case Signed = 'signed';
    case Closed = 'closed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return __('enums.listing_request_status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Requested => 'zinc',
            self::PendingEmailConfirmation => 'amber',
            self::Confirmed => 'blue',
            self::AppointmentPending => 'cyan',
            self::WaitingForInformation => 'orange',
            self::WaitingForApproval => 'yellow',
            self::Approved => 'green',
            self::WaitingForSignature => 'lime',
            self::Signed => 'emerald',
            self::Closed => 'slate',
            self::Rejected => 'red',
        };
    }

    /**
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Requested => [self::PendingEmailConfirmation],
            self::PendingEmailConfirmation => [self::Confirmed],
            self::Confirmed => [self::AppointmentPending, self::WaitingForInformation, self::WaitingForApproval, self::Rejected],
            self::AppointmentPending => [self::WaitingForInformation, self::WaitingForApproval, self::Rejected],
            self::WaitingForInformation => [self::WaitingForApproval, self::Rejected],
            self::WaitingForApproval => [self::Approved, self::Rejected],
            self::Approved => [self::WaitingForSignature, self::Rejected],
            self::WaitingForSignature => [self::Signed, self::Rejected],
            self::Signed => [self::Closed],
            self::Closed => [],
            self::Rejected => [],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Closed, self::Rejected], true);
    }

    public function isActive(): bool
    {
        return ! $this->isTerminal();
    }

    /**
     * @return array<self>
     */
    public static function activeStatuses(): array
    {
        return array_filter(self::cases(), fn (self $status) => $status->isActive());
    }
}
