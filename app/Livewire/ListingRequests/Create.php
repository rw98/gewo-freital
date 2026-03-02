<?php

namespace App\Livewire\ListingRequests;

use App\Enums\ListingRequestStatus;
use App\Models\Listing;
use App\Models\ListingRequest;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\RequestReceivedNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth.public')]
class Create extends Component
{
    public Listing $listing;

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:50')]
    public ?string $phone = null;

    #[Validate('required|string|max:100')]
    public string $first_name = '';

    #[Validate('nullable|string|max:100')]
    public ?string $middle_name = null;

    #[Validate('required|string|max:100')]
    public string $last_name = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $message = null;

    public bool $submitted = false;

    public function mount(Listing $listing): void
    {
        if (! $listing->isPublished()) {
            abort(404);
        }

        $this->listing = $listing;
    }

    public function getTitle(): string
    {
        return __('listing_requests.create_title').' - '.$this->listing->title;
    }

    public function submit(): void
    {
        $this->validate();

        $request = ListingRequest::create([
            'listing_id' => $this->listing->id,
            'email' => $this->email,
            'phone' => $this->phone,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'message' => $this->message,
            'status' => ListingRequestStatus::PendingEmailConfirmation,
        ]);

        // Send notifications
        Notification::route('mail', $this->email)
            ->notify(new RequestReceivedNotification($request));

        Notification::route('mail', $this->email)
            ->notify(new EmailVerificationNotification($request));

        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.listing-requests.create')
            ->title($this->getTitle());
    }
}
