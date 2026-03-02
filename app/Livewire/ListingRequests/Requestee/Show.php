<?php

namespace App\Livewire\ListingRequests\Requestee;

use App\Models\ListingRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth.public')]
class Show extends Component
{
    public ListingRequest $listingRequest;

    public function mount(string $access_token): void
    {
        $this->listingRequest = ListingRequest::query()
            ->where('access_token', $access_token)
            ->with(['listing', 'assignedTo', 'documents', 'appointments.timeslot'])
            ->firstOrFail();
    }

    public function getTitle(): string
    {
        return __('listing_requests.portal_title').' - '.$this->listingRequest->listing->title;
    }

    public function render(): View
    {
        return view('livewire.listing-requests.requestee.show')
            ->title($this->getTitle());
    }
}
