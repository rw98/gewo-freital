<?php

namespace App\Livewire\RentalObjects;

use App\Models\RentalObject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Properties')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $rentalObjects = RentalObject::query()
            ->whereHas('contacts', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('street', 'like', '%'.$this->search.'%')
                        ->orWhere('city', 'like', '%'.$this->search.'%')
                        ->orWhere('postal_code', 'like', '%'.$this->search.'%');
                });
            })
            ->withCount('flats')
            ->orderBy('city')
            ->orderBy('street')
            ->paginate(10);

        return view('livewire.rental-objects.index', [
            'rentalObjects' => $rentalObjects,
        ]);
    }
}
