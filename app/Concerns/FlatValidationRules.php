<?php

namespace App\Concerns;

trait FlatValidationRules
{
    /**
     * Get the validation rules for flats.
     *
     * @return array<string, array<int, string>>
     */
    protected function flatRules(): array
    {
        return [
            'size_sqm' => $this->sizeSqmRules(),
            'rent_cold' => $this->rentColdRules(),
            'utility_cost' => $this->utilityCostRules(),
            'floor' => $this->floorRules(),
            'number' => $this->flatNumberRules(),
            'description' => $this->descriptionRules(),
            'is_wheelchair_accessible' => $this->isWheelchairAccessibleRules(),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function sizeSqmRules(): array
    {
        return ['required', 'numeric', 'min:1', 'max:9999.99'];
    }

    /**
     * @return array<int, string>
     */
    protected function rentColdRules(): array
    {
        return ['required', 'numeric', 'min:0', 'max:99999999.99'];
    }

    /**
     * @return array<int, string>
     */
    protected function utilityCostRules(): array
    {
        return ['required', 'numeric', 'min:0', 'max:99999999.99'];
    }

    /**
     * @return array<int, string>
     */
    protected function floorRules(): array
    {
        return ['required', 'integer', 'min:-5', 'max:200'];
    }

    /**
     * @return array<int, string>
     */
    protected function flatNumberRules(): array
    {
        return ['required', 'string', 'max:20'];
    }

    /**
     * @return array<int, string>
     */
    protected function descriptionRules(): array
    {
        return ['nullable', 'string', 'max:10000'];
    }

    /**
     * @return array<int, string>
     */
    protected function isWheelchairAccessibleRules(): array
    {
        return ['boolean'];
    }
}
