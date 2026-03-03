<?php

namespace App\Enums;

enum EnergyCertificateType: string
{
    case Consumption = 'consumption';
    case Demand = 'demand';

    public function label(): string
    {
        return __('enums.energy_certificate_type.'.$this->value);
    }

    public function description(): string
    {
        return match ($this) {
            self::Consumption => __('enums.energy_certificate_type.consumption_description'),
            self::Demand => __('enums.energy_certificate_type.demand_description'),
        };
    }
}
