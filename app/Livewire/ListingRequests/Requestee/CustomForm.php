<?php

namespace App\Livewire\ListingRequests\Requestee;

use App\Models\ListingRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth.public')]
class CustomForm extends Component
{
    public ListingRequest $listingRequest;

    public function mount(string $access_token): void
    {
        $this->listingRequest = ListingRequest::query()
            ->where('access_token', $access_token)
            ->with(['listing', 'customForm.fields'])
            ->firstOrFail();

        if (! $this->listingRequest->canFillCustomForm()) {
            abort(403, __('forms.custom_form.not_available'));
        }
    }

    public function getTitle(): string
    {
        $formName = $this->listingRequest->customForm?->name ?? __('forms.custom_form.title');

        return $formName.' - '.$this->listingRequest->listing->title;
    }

    public function render(): View
    {
        return view('livewire.listing-requests.requestee.custom-form')
            ->title($this->getTitle());
    }
}
