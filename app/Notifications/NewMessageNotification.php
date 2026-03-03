<?php

namespace App\Notifications;

use App\Models\ListingRequest;
use App\Models\RequestMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ListingRequest $listingRequest,
        public RequestMessage $message
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
        $portalUrl = route('listing-requests.messages', $this->listingRequest->access_token);
        $listingTitle = $this->listingRequest->listing->title;
        $firstName = $this->listingRequest->first_name;
        $messagePreview = Str::limit($this->message->content, 200);

        return (new MailMessage)
            ->subject(__('listing_requests.emails.subject_new_message'))
            ->greeting(__('Hallo :name!', ['name' => $firstName]))
            ->line(__('listing_requests.emails.new_message_line1', ['title' => $listingTitle]))
            ->line('> '.$messagePreview)
            ->action(__('listing_requests.emails.view_message'), $portalUrl)
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
            'message_id' => $this->message->id,
        ];
    }
}
