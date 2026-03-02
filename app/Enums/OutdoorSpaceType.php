<?php

namespace App\Enums;

enum OutdoorSpaceType: string
{
    case Balcony = 'balcony';
    case Terrace = 'terrace';

    public function label(): string
    {
        return __('enums.outdoor_space_type.'.$this->value);
    }
}
