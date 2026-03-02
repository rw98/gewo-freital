<?php

namespace App\Livewire\ListingRequests\Employee;

use App\Enums\ListingRequestStatus;
use App\Models\Listing;
use App\Models\ListingRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    #[Url]
    public string $listing_id = '';

    #[Url]
    public string $search = '';

    public function getTitle(): string
    {
        return __('listing_requests.manage_requests');
    }

    #[Computed]
    public function statuses(): array
    {
        return ListingRequestStatus::cases();
    }

    #[Computed]
    public function listings()
    {
        return Listing::query()
            ->whereHas('requests')
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    #[Computed]
    public function requests()
    {
        return ListingRequest::query()
            ->with(['listing', 'assignedTo'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->listing_id, fn ($q) => $q->where('listing_id', $this->listing_id))
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('email', 'like', "%{$this->search}%")
                        ->orWhere('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('requested_at')
            ->paginate(20);
    }

    public function resetFilters(): void
    {
        $this->reset(['status', 'listing_id', 'search']);
    }

    public function render(): View
    {
        return view('livewire.listing-requests.employee.index')
            ->title($this->getTitle());
    }
}
