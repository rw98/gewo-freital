<?php

namespace App\Livewire\ListingRequests\Requestee;

use App\Enums\RequestAppointmentStatus;
use App\Models\ListingRequest;
use App\Models\RequestAppointment;
use App\Models\RequestTimeslot;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth.public')]
class Appointments extends Component
{
    public ListingRequest $listingRequest;

    public function mount(string $access_token): void
    {
        $this->listingRequest = ListingRequest::query()
            ->where('access_token', $access_token)
            ->with(['listing', 'appointments.timeslot'])
            ->firstOrFail();
    }

    public function getTitle(): string
    {
        return __('listing_requests.appointments').' - '.$this->listingRequest->listing->title;
    }

    #[Computed]
    public function availableTimeslots()
    {
        return RequestTimeslot::query()
            ->where('listing_id', $this->listingRequest->listing_id)
            ->bookable()
            ->orderBy('starts_at')
            ->get()
            ->filter(fn (RequestTimeslot $slot) => $slot->hasAvailableSlots());
    }

    #[Computed]
    public function bookedAppointments()
    {
        return $this->listingRequest->appointments()
            ->with('timeslot')
            ->get()
            ->sortByDesc(fn ($a) => $a->timeslot?->starts_at);
    }

    public function book(RequestTimeslot $timeslot): void
    {
        // Check if already has an active appointment
        $hasActive = $this->listingRequest->appointments()
            ->whereIn('status', [
                RequestAppointmentStatus::Pending,
                RequestAppointmentStatus::Confirmed,
            ])
            ->whereHas('timeslot', fn ($q) => $q->where('starts_at', '>', now()))
            ->exists();

        if ($hasActive) {
            session()->flash('error', __('Sie haben bereits einen aktiven Termin.'));

            return;
        }

        if (! $timeslot->isBookable()) {
            session()->flash('error', __('Dieser Termin ist nicht mehr verfügbar.'));

            return;
        }

        RequestAppointment::create([
            'listing_request_id' => $this->listingRequest->id,
            'timeslot_id' => $timeslot->id,
            'status' => RequestAppointmentStatus::Pending,
        ]);

        $this->listingRequest->refresh();

        session()->flash('success', __('listing_requests.appointment_booked'));
    }

    public function cancel(RequestAppointment $appointment): void
    {
        if ($appointment->listing_request_id !== $this->listingRequest->id) {
            abort(403);
        }

        $appointment->cancel(__('Vom Interessenten abgesagt'));
        $this->listingRequest->refresh();

        session()->flash('success', __('listing_requests.appointment_cancelled'));
    }

    public function render(): View
    {
        return view('livewire.listing-requests.requestee.appointments')
            ->title($this->getTitle());
    }
}
