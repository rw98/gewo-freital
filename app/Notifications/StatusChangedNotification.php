<?php

namespace App\Notifications;

use App\Enums\ListingRequestStatus;
use App\Models\ListingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Statuses that should trigger a notification to the requestee.
     *
     * @var array<ListingRequestStatus>
     */
    private const NOTIFIABLE_STATUSES = [
        ListingRequestStatus::Confirmed,
        ListingRequestStatus::AppointmentPending,
        ListingRequestStatus::WaitingForInformation,
        ListingRequestStatus::WaitingForSignature,
        ListingRequestStatus::Signed,
        ListingRequestStatus::Rejected,
        ListingRequestStatus::Closed,
    ];

    public function __construct(
        public ListingRequest $listingRequest
    ) {
        $this->afterCommit();
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        return in_array($this->listingRequest->status, self::NOTIFIABLE_STATUSES, true);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $portalUrl = route('listing-requests.portal', $this->listingRequest->access_token);
        $status = $this->listingRequest->status;
        $listingTitle = $this->listingRequest->listing->title;
        $firstName = $this->listingRequest->first_name;

        $message = (new MailMessage)
            ->subject($this->getSubject($status))
            ->greeting(__('Hallo :name!', ['name' => $firstName]));

        // Add status-specific content
        match ($status) {
            ListingRequestStatus::Confirmed => $message
                ->line(__('listing_requests.emails.confirmed_line1', ['title' => $listingTitle]))
                ->line(__('listing_requests.emails.confirmed_line2')),

            ListingRequestStatus::AppointmentPending => $message
                ->line(__('listing_requests.emails.appointment_pending_line1', ['title' => $listingTitle]))
                ->line(__('listing_requests.emails.appointment_pending_line2')),

            ListingRequestStatus::WaitingForInformation => $message
                ->line(__('listing_requests.emails.waiting_for_information_line1', ['title' => $listingTitle]))
                ->line(__('listing_requests.emails.waiting_for_information_line2')),

            ListingRequestStatus::WaitingForSignature => $message
                ->line(__('listing_requests.emails.waiting_for_signature_line1', ['title' => $listingTitle]))
                ->line(__('listing_requests.emails.waiting_for_signature_line2')),

            ListingRequestStatus::Signed => $message
                ->line(__('listing_requests.emails.signed_line1', ['title' => $listingTitle]))
                ->line(__('listing_requests.emails.signed_line2')),

            ListingRequestStatus::Rejected => $message
                ->line(__('listing_requests.emails.rejected_line1', ['title' => $listingTitle]))
                ->when($this->listingRequest->rejection_reason, fn ($m) => $m
                    ->line(__('listing_requests.emails.rejected_reason', ['reason' => $this->listingRequest->rejection_reason]))
                ),

            ListingRequestStatus::Closed => $message
                ->line(__('listing_requests.emails.closed_line1', ['title' => $listingTitle]))
                ->line(__('listing_requests.emails.closed_line2')),

            default => $message
                ->line(__('Der Status Ihrer Anfrage für die Wohnung ":title" hat sich geändert.', ['title' => $listingTitle]))
                ->line(__('Neuer Status: :status', ['status' => $status->label()])),
        };

        return $message
            ->action(__('Anfrage ansehen'), $portalUrl)
            ->salutation(__('Mit freundlichen Grüßen, :name', ['name' => config('app.name')]));
    }

    private function getSubject(ListingRequestStatus $status): string
    {
        return match ($status) {
            ListingRequestStatus::Confirmed => __('listing_requests.emails.subject_confirmed'),
            ListingRequestStatus::AppointmentPending => __('listing_requests.emails.subject_appointment_pending'),
            ListingRequestStatus::WaitingForInformation => __('listing_requests.emails.subject_waiting_for_information'),
            ListingRequestStatus::WaitingForSignature => __('listing_requests.emails.subject_waiting_for_signature'),
            ListingRequestStatus::Signed => __('listing_requests.emails.subject_signed'),
            ListingRequestStatus::Rejected => __('listing_requests.emails.subject_rejected'),
            ListingRequestStatus::Closed => __('listing_requests.emails.subject_closed'),
            default => __('listing_requests.notification_status_changed'),
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'listing_request_id' => $this->listingRequest->id,
            'status' => $this->listingRequest->status->value,
        ];
    }
}
