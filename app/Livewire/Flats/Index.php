<?php

namespace App\Livewire\Flats;

use App\Models\Flat;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('All Flats')]
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
        $flats = Flat::query()
            ->with('rentalObject')
            ->whereHas('rentalObject.contacts', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orWhereHas('tenants', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('rentalObject', function ($rq) {
                            $rq->where('street', 'like', '%'.$this->search.'%')
                                ->orWhere('city', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('floor')
            ->orderBy('number')
            ->paginate(10);

        return view('livewire.flats.index', [
            'flats' => $flats,
        ]);
    }
}
