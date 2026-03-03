<?php

namespace App\Enums;

enum IntegrationType: string
{
    case Immoscout = 'immoscout';
    case Immowelt = 'immowelt';

    public function label(): string
    {
        return __('enums.integration_type.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Immoscout => 'blue',
            self::Immowelt => 'orange',
        };
    }
}
