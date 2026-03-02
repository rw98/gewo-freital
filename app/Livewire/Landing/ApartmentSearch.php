<?php

namespace App\Livewire\Landing;

use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ApartmentSearch extends Component
{
    public string $city = '';

    public string $minRooms = '';

    public ?int $maxRent = null;

    public function search(): void
    {
        $params = array_filter([
            'city' => $this->city,
            'minRooms' => $this->minRooms,
            'maxRent' => $this->maxRent,
        ]);

        $this->redirect(route('listings.index', $params), navigate: true);
    }

    #[Computed]
    public function cities()
    {
        return Listing::query()
            ->published()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }

    #[Computed]
    public function featuredListings()
    {
        return Listing::query()
            ->published()
            ->with('images')
            ->latest('published_at')
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function totalCount(): int
    {
        return Listing::query()->published()->count();
    }

    public function render(): View
    {
        return view('livewire.landing.apartment-search');
    }
}
