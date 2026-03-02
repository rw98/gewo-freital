<?php

namespace App\Livewire\Flats;

use App\Models\Flat;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Flat Details')]
class Show extends Component
{
    public Flat $flat;

    public function mount(Flat $flat): void
    {
        $this->authorize('view', $flat);
        $this->flat = $flat->load('rentalObject');
    }

    #[Computed]
    public function tenants()
    {
        return $this->flat->tenants;
    }

    #[Computed]
    public function rooms()
    {
        return $this->flat->rooms;
    }

    #[Computed]
    public function notes()
    {
        return $this->flat->notes()->with('user')->latest()->get();
    }

    #[Computed]
    public function canEdit(): bool
    {
        return auth()->user()->can('update', $this->flat);
    }

    #[Computed]
    public function canDelete(): bool
    {
        return auth()->user()->can('delete', $this->flat);
    }

    public function render(): View
    {
        return view('livewire.flats.show');
    }
}
