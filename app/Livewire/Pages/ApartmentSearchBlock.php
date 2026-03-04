<?php

namespace App\Livewire\Pages;

use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ApartmentSearchBlock extends Component
{
    public string $title = '';

    public string $description = '';

    public bool $showFeatured = true;

    public int $featuredCount = 3;

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

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    #[Computed]
    public function cities(): \Illuminate\Support\Collection
    {
        return Listing::query()
            ->published()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }

    /**
     * @return Collection<int, Listing>
     */
    #[Computed]
    public function featuredListings(): Collection
    {
        if (! $this->showFeatured) {
            return new Collection;
        }

        return Listing::query()
            ->published()
            ->with('images')
            ->latest('published_at')
            ->limit($this->featuredCount)
            ->get();
    }

    #[Computed]
    public function totalCount(): int
    {
        return Listing::query()->published()->count();
    }

    public function render(): View
    {
        return view('livewire.pages.apartment-search-block');
    }
}
