<?php

namespace App\Notifications;

use App\Models\ListingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ListingRequest $listingRequest,
        public ?string $reason = null
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
        $message = (new MailMessage)
            ->subject(__('listing_requests.notification_listing_closed'))
            ->greeting(__('Hallo :name!', ['name' => $this->listingRequest->first_name]))
            ->line(__('Leider wurde das Inserat ":title" geschlossen und steht nicht mehr zur Verfügung.', [
                'title' => $this->listingRequest->listing->title,
            ]));

        if ($this->reason) {
            $message->line($this->reason);
        }

        return $message
            ->line(__('Vielen Dank für Ihr Interesse.'))
            ->action(__('Alle Wohnungen ansehen'), route('listings.index'))
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
            'reason' => $this->reason,
        ];
    }
}
