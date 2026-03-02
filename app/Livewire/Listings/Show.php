<?php

namespace App\Livewire\Listings;

use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class Show extends Component
{
    public Listing $listing;

    public function mount(Listing $listing): void
    {
        if (! $listing->isPublished()) {
            abort(404);
        }

        $this->listing = $listing;
    }

    public function getTitle(): string
    {
        return $this->listing->title.' - Wohnungsangebot';
    }

    #[Computed]
    public function images()
    {
        return $this->listing->images()->orderBy('order')->get();
    }

    #[Computed]
    public function similarListings()
    {
        return Listing::query()
            ->published()
            ->where('id', '!=', $this->listing->id)
            ->where('city', $this->listing->city)
            ->limit(3)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.listings.show')
            ->title($this->getTitle());
    }
}
