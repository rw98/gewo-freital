<?php

namespace App\Services;

use App\Models\RequestAppointment;

class ICalService
{
    /**
     * Generate an iCalendar file for an appointment.
     */
    public function generateAppointmentIcal(RequestAppointment $appointment): string
    {
        $timeslot = $appointment->timeslot;
        $listingRequest = $appointment->listingRequest;
        $listing = $listingRequest->listing;

        $uid = $appointment->id.'@'.parse_url(config('app.url'), PHP_URL_HOST);
        $now = now()->format('Ymd\THis\Z');
        $start = $timeslot->starts_at->utc()->format('Ymd\THis\Z');
        $end = $timeslot->ends_at->utc()->format('Ymd\THis\Z');
        $location = $timeslot->location ?? $listing->fullAddress();
        $summary = __('Besichtigung: :title', ['title' => $listing->title]);
        $description = implode('\n', [
            __('Besichtigungstermin für: :title', ['title' => $listing->title]),
            __('Adresse: :address', ['address' => $listing->fullAddress()]),
            '',
            __('Ihr Ansprechpartner ist im Portal zu finden.'),
        ]);

        $ical = implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//'.config('app.name').'//Appointment//DE',
            'CALSCALE:GREGORIAN',
            'METHOD:REQUEST',
            'BEGIN:VEVENT',
            'UID:'.$this->escape($uid),
            'DTSTAMP:'.$now,
            'DTSTART:'.$start,
            'DTEND:'.$end,
            'SUMMARY:'.$this->escape($summary),
            'DESCRIPTION:'.$this->escape($description),
            'LOCATION:'.$this->escape($location),
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
            'BEGIN:VALARM',
            'TRIGGER:-PT1H',
            'ACTION:DISPLAY',
            'DESCRIPTION:'.$this->escape(__('Termin in einer Stunde: :title', ['title' => $listing->title])),
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        return $ical;
    }

    /**
     * Get the filename for an iCal appointment file.
     */
    public function getFilename(RequestAppointment $appointment): string
    {
        $date = $appointment->timeslot->starts_at->format('Y-m-d');

        return "besichtigung-{$date}.ics";
    }

    /**
     * Escape special characters for iCal format.
     */
    private function escape(string $text): string
    {
        return str_replace(
            ['\\', ';', ',', "\n", "\r"],
            ['\\\\', '\;', '\,', '\n', ''],
            $text
        );
    }
}
