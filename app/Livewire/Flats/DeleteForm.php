<?php

namespace App\Livewire\Flats;

use App\Models\Flat;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DeleteForm extends Component
{
    public Flat $flat;

    public function delete(): void
    {
        $this->authorize('delete', $this->flat);

        $rentalObject = $this->flat->rentalObject;

        $this->flat->delete();

        $this->redirect(route('rental-objects.show', $rentalObject), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.flats.delete-form');
    }
}
