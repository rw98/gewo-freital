<?php

namespace App\Livewire\Flats;

use App\Concerns\FlatValidationRules;
use App\Models\Flat;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Flat')]
class Edit extends Component
{
    use FlatValidationRules;

    public Flat $flat;

    public string $size_sqm = '';

    public string $rent_cold = '';

    public string $utility_cost = '';

    public string $floor = '';

    public string $number = '';

    public string $description = '';

    public bool $is_wheelchair_accessible = false;

    public function mount(Flat $flat): void
    {
        $this->authorize('update', $flat);

        $this->flat = $flat->load('rentalObject');
        $this->size_sqm = (string) $flat->size_sqm;
        $this->rent_cold = (string) $flat->rent_cold;
        $this->utility_cost = (string) $flat->utility_cost;
        $this->floor = (string) $flat->floor;
        $this->number = $flat->number;
        $this->description = $flat->description ?? '';
        $this->is_wheelchair_accessible = $flat->is_wheelchair_accessible;
    }

    public function save(): void
    {
        $this->authorize('update', $this->flat);

        $validated = $this->validate($this->flatRules());

        $this->flat->update($validated);

        $this->redirect(route('flats.show', $this->flat), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.flats.edit');
    }
}
