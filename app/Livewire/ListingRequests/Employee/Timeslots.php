<?php

namespace App\Livewire\ListingRequests\Employee;

use App\Models\Listing;
use App\Models\RequestTimeslot;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Timeslots extends Component
{
    public Listing $listing;

    #[Validate('required|date|after:now')]
    public string $starts_at = '';

    #[Validate('required|date|after:starts_at')]
    public string $ends_at = '';

    #[Validate('required|integer|min:1|max:20')]
    public int $max_attendees = 1;

    #[Validate('nullable|string|max:255')]
    public ?string $location = null;

    #[Validate('nullable|string|max:500')]
    public ?string $notes = null;

    public bool $showCreateModal = false;

    public function mount(Listing $listing): void
    {
        Gate::authorize('manageTimeslots', [RequestTimeslot::class, $listing]);

        $this->listing = $listing;
    }

    public function getTitle(): string
    {
        return __('listing_requests.manage_timeslots').' - '.$this->listing->title;
    }

    #[Computed]
    public function timeslots()
    {
        return $this->listing->timeslots()
            ->with(['createdBy', 'appointments.listingRequest'])
            ->orderBy('starts_at')
            ->get();
    }

    public function openCreateModal(): void
    {
        $this->reset(['starts_at', 'ends_at', 'max_attendees', 'location', 'notes']);
        $this->max_attendees = 1;
        $this->showCreateModal = true;
    }

    public function create(): void
    {
        $this->validate();

        RequestTimeslot::create([
            'listing_id' => $this->listing->id,
            'created_by' => auth()->id(),
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'max_attendees' => $this->max_attendees,
            'location' => $this->location,
            'notes' => $this->notes,
            'is_active' => true,
        ]);

        $this->showCreateModal = false;
        $this->reset(['starts_at', 'ends_at', 'max_attendees', 'location', 'notes']);

        session()->flash('success', __('listing_requests.timeslot_created'));
    }

    public function toggleActive(RequestTimeslot $timeslot): void
    {
        $timeslot->update(['is_active' => ! $timeslot->is_active]);
    }

    public function delete(RequestTimeslot $timeslot): void
    {
        if ($timeslot->appointments()->whereNotIn('status', ['cancelled'])->exists()) {
            session()->flash('error', __('Cannot delete timeslot with active appointments.'));

            return;
        }

        $timeslot->delete();

        session()->flash('success', __('listing_requests.timeslot_deleted'));
    }

    public function render(): View
    {
        return view('livewire.listing-requests.employee.timeslots')
            ->title($this->getTitle());
    }
}
