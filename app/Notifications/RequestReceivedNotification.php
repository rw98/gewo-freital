<?php

namespace App\Notifications;

use App\Models\ListingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ListingRequest $listingRequest
    ) {
        $this->afterCommit();
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
        $listing = $this->listingRequest->listing;
        $portalUrl = route('listing-requests.portal', $this->listingRequest->access_token);

        return (new MailMessage)
            ->subject(__('listing_requests.notification_request_received'))
            ->greeting(__('Hallo :name!', ['name' => $this->listingRequest->first_name]))
            ->line(__('Vielen Dank für Ihre Anfrage für die Wohnung ":title".', ['title' => $listing->title]))
            ->line(__('Ihre Anfrage wurde erfolgreich empfangen.'))
            ->line(__('Sobald Ihre E-Mail-Adresse bestätigt ist, wird Ihre Anfrage bearbeitet.'))
            ->action(__('Anfrage ansehen'), $portalUrl)
            ->line(__('Sie können den Status Ihrer Anfrage jederzeit über den obigen Link einsehen.'))
            ->salutation(__('Mit freundlichen Grüßen, :name', ['name' => config('app.name')]));
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
            'listing_id' => $this->listingRequest->listing_id,
        ];
    }
}
