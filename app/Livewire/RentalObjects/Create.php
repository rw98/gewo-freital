<?php

namespace App\Livewire\RentalObjects;

use App\Concerns\RentalObjectValidationRules;
use App\Models\RentalObject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Add Property')]
class Create extends Component
{
    use RentalObjectValidationRules;

    public string $object_number = '';

    public string $street = '';

    public string $number = '';

    public string $city = '';

    public string $postal_code = '';

    public string $country = 'DE';

    public bool $has_elevator = false;

    public ?int $year_built = null;

    public function save(): void
    {
        $this->authorize('create', RentalObject::class);

        $validated = $this->validate($this->rentalObjectRules());

        $rentalObject = RentalObject::create($validated);

        $rentalObject->contacts()->attach(Auth::id(), ['role' => 'owner']);

        $this->redirect(route('rental-objects.show', $rentalObject), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.rental-objects.create');
    }
}
