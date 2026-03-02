<?php

namespace App\Livewire\RentalObjects;

use App\Concerns\RentalObjectValidationRules;
use App\Enums\EnergyCertificateType;
use App\Enums\EnergySource;
use App\Models\RentalObject;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Property')]
class Edit extends Component
{
    use RentalObjectValidationRules;

    public RentalObject $rentalObject;

    public string $object_number = '';

    public string $street = '';

    public string $number = '';

    public string $city = '';

    public string $postal_code = '';

    public string $country = '';

    public bool $has_elevator = false;

    public ?int $year_built = null;

    public ?string $energy_certificate_type = null;

    public ?string $energy_consumption_kwh = null;

    public ?string $energy_source = null;

    public ?string $energy_certificate_valid_until = null;

    #[Computed]
    public function energyCertificateTypes(): array
    {
        return EnergyCertificateType::cases();
    }

    #[Computed]
    public function energySources(): array
    {
        return EnergySource::cases();
    }

    public function mount(RentalObject $rentalObject): void
    {
        $this->authorize('update', $rentalObject);

        $this->rentalObject = $rentalObject;
        $this->object_number = $rentalObject->object_number;
        $this->street = $rentalObject->street;
        $this->number = $rentalObject->number;
        $this->city = $rentalObject->city;
        $this->postal_code = $rentalObject->postal_code;
        $this->country = $rentalObject->country;
        $this->has_elevator = $rentalObject->has_elevator;
        $this->year_built = $rentalObject->year_built;
        $this->energy_certificate_type = $rentalObject->energy_certificate_type?->value;
        $this->energy_consumption_kwh = $rentalObject->energy_consumption_kwh;
        $this->energy_source = $rentalObject->energy_source?->value;
        $this->energy_certificate_valid_until = $rentalObject->energy_certificate_valid_until?->format('Y-m-d');
    }

    public function save(): void
    {
        $this->authorize('update', $this->rentalObject);

        $validated = $this->validate($this->rentalObjectRules($this->rentalObject->id));

        $this->rentalObject->update($validated);

        $this->redirect(route('rental-objects.show', $this->rentalObject), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.rental-objects.edit');
    }
}
