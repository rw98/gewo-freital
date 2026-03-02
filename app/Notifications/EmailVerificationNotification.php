<?php

namespace App\Notifications;

use App\Models\ListingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class EmailVerificationNotification extends Notification implements ShouldQueue
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
        $verificationUrl = $this->verificationUrl();

        return (new MailMessage)
            ->subject(__('listing_requests.notification_email_verification'))
            ->greeting(__('Hallo :name!', ['name' => $this->listingRequest->first_name]))
            ->line(__('Bitte bestätigen Sie Ihre E-Mail-Adresse, indem Sie auf den folgenden Button klicken.'))
            ->action(__('E-Mail-Adresse bestätigen'), $verificationUrl)
            ->line(__('Dieser Link ist 24 Stunden gültig.'))
            ->line(__('Wenn Sie diese Anfrage nicht gestellt haben, können Sie diese E-Mail ignorieren.'))
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
        ];
    }

    /**
     * Generate the signed verification URL.
     */
    protected function verificationUrl(): string
    {
        return URL::temporarySignedRoute(
            'listing-requests.verify-email',
            now()->addHours(24),
            [
                'listingRequest' => $this->listingRequest->id,
            ]
        );
    }
}
