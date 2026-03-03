<?php

namespace App\Enums;

enum EnergySource: string
{
    case Gas = 'gas';
    case Oil = 'oil';
    case DistrictHeating = 'district_heating';
    case Electricity = 'electricity';
    case HeatPump = 'heat_pump';
    case Pellets = 'pellets';
    case Wood = 'wood';
    case Solar = 'solar';
    case Geothermal = 'geothermal';
    case Other = 'other';

    public function label(): string
    {
        return __('enums.energy_source.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Gas => 'fire',
            self::Oil => 'beaker',
            self::DistrictHeating => 'building-office-2',
            self::Electricity => 'bolt',
            self::HeatPump => 'arrow-path',
            self::Pellets => 'cube',
            self::Wood => 'cube',
            self::Solar => 'sun',
            self::Geothermal => 'globe-europe-africa',
            self::Other => 'question-mark-circle',
        };
    }
}
