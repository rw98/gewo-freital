<?php

namespace App\Livewire\Flats;

use App\Concerns\FlatValidationRules;
use App\Models\Flat;
use App\Models\RentalObject;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Add Flat')]
class Create extends Component
{
    use FlatValidationRules;

    public RentalObject $rentalObject;

    public string $size_sqm = '';

    public string $rent_cold = '';

    public string $utility_cost = '';

    public string $floor = '0';

    public string $number = '';

    public string $description = '';

    public bool $is_wheelchair_accessible = false;

    public function mount(RentalObject $rentalObject): void
    {
        $this->authorize('create', [Flat::class, $rentalObject]);
        $this->rentalObject = $rentalObject;
    }

    public function save(): void
    {
        $this->authorize('create', [Flat::class, $this->rentalObject]);

        $validated = $this->validate($this->flatRules());

        $flat = $this->rentalObject->flats()->create($validated);

        $this->redirect(route('flats.show', $flat), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.flats.create');
    }
}
