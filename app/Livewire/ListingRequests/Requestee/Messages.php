<?php

namespace App\Livewire\ListingRequests\Requestee;

use App\Models\ListingRequest;
use App\Models\RequestMessage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth.public')]
class Messages extends Component
{
    public ListingRequest $listingRequest;

    #[Validate('required|string|max:2000')]
    public string $content = '';

    public function mount(string $access_token): void
    {
        $this->listingRequest = ListingRequest::query()
            ->where('access_token', $access_token)
            ->with(['listing', 'messages.user'])
            ->firstOrFail();

        // Mark employee messages as read
        $this->listingRequest->messages()
            ->where('sender_type', 'employee')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getTitle(): string
    {
        return __('listing_requests.messages').' - '.$this->listingRequest->listing->title;
    }

    #[Computed]
    public function conversationMessages()
    {
        return $this->listingRequest->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function send(): void
    {
        $this->validate();

        RequestMessage::create([
            'listing_request_id' => $this->listingRequest->id,
            'user_id' => null,
            'sender_type' => 'requestee',
            'content' => $this->content,
        ]);

        $this->reset('content');
        $this->listingRequest->refresh();

        session()->flash('success', __('listing_requests.message_sent'));
    }

    public function render(): View
    {
        return view('livewire.listing-requests.requestee.messages')
            ->title($this->getTitle());
    }
}
