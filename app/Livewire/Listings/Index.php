<?php

namespace App\Livewire\Listings;

use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public')]
#[Title('Wohnungsangebote')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $city = '';

    #[Url]
    public ?int $minRooms = null;

    #[Url]
    public ?int $maxRent = null;

    #[Url]
    public ?int $minSize = null;

    #[Url]
    public string $sortBy = 'newest';

    public function mount(): void
    {
        // Restore from session if no URL params
        if (! request()->hasAny(['search', 'city', 'minRooms', 'maxRent', 'minSize', 'sortBy'])) {
            $this->search = session('listings.search', '');
            $this->city = session('listings.city', '');
            $this->minRooms = session('listings.minRooms');
            $this->maxRent = session('listings.maxRent');
            $this->minSize = session('listings.minSize');
            $this->sortBy = session('listings.sortBy', 'newest');
        }
    }

    public function updatedSearch(): void
    {
        session(['listings.search' => $this->search]);
        $this->resetPage();
    }

    public function updatedCity(): void
    {
        session(['listings.city' => $this->city]);
        $this->resetPage();
    }

    public function updatedMinRooms(): void
    {
        session(['listings.minRooms' => $this->minRooms]);
        $this->resetPage();
    }

    public function updatedMaxRent(): void
    {
        session(['listings.maxRent' => $this->maxRent]);
        $this->resetPage();
    }

    public function updatedMinSize(): void
    {
        session(['listings.minSize' => $this->minSize]);
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        session(['listings.sortBy' => $this->sortBy]);
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        session()->forget('listings.search');
        $this->resetPage();
    }

    public function clearCity(): void
    {
        $this->city = '';
        session()->forget('listings.city');
        $this->resetPage();
    }

    public function clearMinRooms(): void
    {
        $this->minRooms = null;
        session()->forget('listings.minRooms');
        $this->resetPage();
    }

    public function clearMaxRent(): void
    {
        $this->maxRent = null;
        session()->forget('listings.maxRent');
        $this->resetPage();
    }

    public function clearMinSize(): void
    {
        $this->minSize = null;
        session()->forget('listings.minSize');
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'city', 'minRooms', 'maxRent', 'minSize', 'sortBy']);
        session()->forget([
            'listings.search',
            'listings.city',
            'listings.minRooms',
            'listings.maxRent',
            'listings.minSize',
            'listings.sortBy',
        ]);
        $this->resetPage();
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->city !== ''
            || $this->minRooms !== null
            || $this->maxRent !== null
            || $this->minSize !== null;
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return collect([
            $this->search !== '',
            $this->city !== '',
            $this->minRooms !== null,
            $this->maxRent !== null,
            $this->minSize !== null,
        ])->filter()->count();
    }

    public function render(): View
    {
        $listings = Listing::query()
            ->published()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%')
                        ->orWhere('street', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->city, function ($query) {
                $query->where('city', $this->city);
            })
            ->when($this->minRooms, function ($query) {
                $query->where('rooms', '>=', $this->minRooms);
            })
            ->when($this->maxRent, function ($query) {
                $query->whereRaw('(rent_cold + utility_cost) <= ?', [$this->maxRent]);
            })
            ->when($this->minSize, function ($query) {
                $query->where('size_sqm', '>=', $this->minSize);
            })
            ->when($this->sortBy === 'newest', function ($query) {
                $query->orderByDesc('published_at');
            })
            ->when($this->sortBy === 'rent_asc', function ($query) {
                $query->orderByRaw('rent_cold + utility_cost ASC');
            })
            ->when($this->sortBy === 'rent_desc', function ($query) {
                $query->orderByRaw('rent_cold + utility_cost DESC');
            })
            ->when($this->sortBy === 'size_asc', function ($query) {
                $query->orderBy('size_sqm');
            })
            ->when($this->sortBy === 'size_desc', function ($query) {
                $query->orderByDesc('size_sqm');
            })
            ->with('images')
            ->paginate(12);

        $cities = Listing::query()
            ->published()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('livewire.listings.index', [
            'listings' => $listings,
            'cities' => $cities,
        ]);
    }
}
