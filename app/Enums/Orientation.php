<?php

namespace App\Enums;

enum Orientation: string
{
    case North = 'N';
    case NorthEast = 'NE';
    case East = 'E';
    case SouthEast = 'SE';
    case South = 'S';
    case SouthWest = 'SW';
    case West = 'W';
    case NorthWest = 'NW';

    public function label(): string
    {
        return __('enums.orientation.'.$this->value);
    }
}
