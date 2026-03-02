<?php

namespace App\Livewire\RentalObjects;

use App\Models\RentalObject;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DeleteForm extends Component
{
    public RentalObject $rentalObject;

    public function delete(): void
    {
        $this->authorize('delete', $this->rentalObject);

        $this->rentalObject->delete();

        $this->redirect(route('rental-objects.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.rental-objects.delete-form');
    }
}
