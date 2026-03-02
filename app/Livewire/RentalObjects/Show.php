<?php

namespace App\Livewire\RentalObjects;

use App\Models\RentalObject;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Property Details')]
class Show extends Component
{
    public RentalObject $rentalObject;

    public function mount(RentalObject $rentalObject): void
    {
        $this->authorize('view', $rentalObject);
        $this->rentalObject = $rentalObject;
    }

    #[Computed]
    public function flats()
    {
        return $this->rentalObject->flats()
            ->orderBy('floor')
            ->orderBy('number')
            ->get();
    }

    #[Computed]
    public function contacts()
    {
        return $this->rentalObject->contacts;
    }

    #[Computed]
    public function canEdit(): bool
    {
        return auth()->user()->can('update', $this->rentalObject);
    }

    #[Computed]
    public function canDelete(): bool
    {
        return auth()->user()->can('delete', $this->rentalObject);
    }

    #[Computed]
    public function canCreateFlat(): bool
    {
        return auth()->user()->can('create', [\App\Models\Flat::class, $this->rentalObject]);
    }

    public function render(): View
    {
        return view('livewire.rental-objects.show');
    }
}
