<?php

namespace App\Notifications;

use App\Models\RequestAppointment;
use App\Services\ICalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RequestAppointment $appointment
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
        $listingRequest = $this->appointment->listingRequest;
        $timeslot = $this->appointment->timeslot;
        $listing = $listingRequest->listing;
        $portalUrl = route('listing-requests.appointments', $listingRequest->access_token);

        $icalService = app(ICalService::class);
        $icalContent = $icalService->generateAppointmentIcal($this->appointment);
        $icalFilename = $icalService->getFilename($this->appointment);

        return (new MailMessage)
            ->subject(__('listing_requests.notification_appointment_confirmed'))
            ->greeting(__('Hallo :name!', ['name' => $listingRequest->first_name]))
            ->line(__('Ihr Besichtigungstermin für die Wohnung ":title" wurde bestätigt.', [
                'title' => $listing->title,
            ]))
            ->line(__('Datum: :date', [
                'date' => $timeslot->starts_at->format('l, d.m.Y'),
            ]))
            ->line(__('Uhrzeit: :time Uhr', [
                'time' => $timeslot->starts_at->format('H:i').' - '.$timeslot->ends_at->format('H:i'),
            ]))
            ->line(__('Ort: :location', [
                'location' => $timeslot->location ?? $listing->fullAddress(),
            ]))
            ->action(__('Termin ansehen'), $portalUrl)
            ->line(__('Die Termindatei können Sie Ihrem Kalender hinzufügen.'))
            ->salutation(__('Mit freundlichen Grüßen, :name', ['name' => config('app.name')]))
            ->attachData($icalContent, $icalFilename, [
                'mime' => 'text/calendar; charset=utf-8; method=REQUEST',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'listing_request_id' => $this->appointment->listing_request_id,
        ];
    }
}
