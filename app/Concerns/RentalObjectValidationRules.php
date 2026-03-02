<?php

namespace App\Concerns;

use App\Enums\EnergyCertificateType;
use App\Enums\EnergySource;
use App\Models\RentalObject;
use Illuminate\Validation\Rule;

trait RentalObjectValidationRules
{
    /**
     * Get the validation rules for rental objects.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|string>>
     */
    protected function rentalObjectRules(?string $rentalObjectId = null): array
    {
        return [
            'object_number' => $this->objectNumberRules($rentalObjectId),
            'street' => $this->streetRules(),
            'number' => $this->numberRules(),
            'city' => $this->cityRules(),
            'postal_code' => $this->postalCodeRules(),
            'country' => $this->countryRules(),
            'has_elevator' => $this->hasElevatorRules(),
            'year_built' => $this->yearBuiltRules(),
            'energy_certificate_type' => $this->energyCertificateTypeRules(),
            'energy_consumption_kwh' => $this->energyConsumptionRules(),
            'energy_source' => $this->energySourceRules(),
            'energy_certificate_valid_until' => $this->energyCertificateValidUntilRules(),
        ];
    }

    /**
     * @return array<int, \Illuminate\Contracts\Validation\Rule|string>
     */
    protected function objectNumberRules(?string $rentalObjectId = null): array
    {
        return [
            'required',
            'string',
            'max:50',
            $rentalObjectId === null
                ? Rule::unique(RentalObject::class)
                : Rule::unique(RentalObject::class)->ignore($rentalObjectId),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function streetRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * @return array<int, string>
     */
    protected function numberRules(): array
    {
        return ['required', 'string', 'max:20'];
    }

    /**
     * @return array<int, string>
     */
    protected function cityRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * @return array<int, string>
     */
    protected function postalCodeRules(): array
    {
        return ['required', 'string', 'max:20'];
    }

    /**
     * @return array<int, string>
     */
    protected function countryRules(): array
    {
        return ['required', 'string', 'max:2'];
    }

    /**
     * @return array<int, string>
     */
    protected function hasElevatorRules(): array
    {
        return ['boolean'];
    }

    /**
     * @return array<int, string>
     */
    protected function yearBuiltRules(): array
    {
        return ['nullable', 'integer', 'min:1800', 'max:'.date('Y')];
    }

    /**
     * @return array<int, \Illuminate\Contracts\Validation\Rule|string>
     */
    protected function energyCertificateTypeRules(): array
    {
        return ['nullable', Rule::enum(EnergyCertificateType::class)];
    }

    /**
     * @return array<int, string>
     */
    protected function energyConsumptionRules(): array
    {
        return ['nullable', 'numeric', 'min:0', 'max:999.99'];
    }

    /**
     * @return array<int, \Illuminate\Contracts\Validation\Rule|string>
     */
    protected function energySourceRules(): array
    {
        return ['nullable', Rule::enum(EnergySource::class)];
    }

    /**
     * @return array<int, string>
     */
    protected function energyCertificateValidUntilRules(): array
    {
        return ['nullable', 'date'];
    }
}
