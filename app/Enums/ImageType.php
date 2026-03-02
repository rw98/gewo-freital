<?php

namespace App\Enums;

enum ImageType: string
{
    case Exterior = 'exterior';
    case Interior = 'interior';
    case Layout = 'layout';
    case Floor = 'floor';
    case Entrance = 'entrance';
    case LivingRoom = 'living_room';
    case Bedroom = 'bedroom';
    case Kitchen = 'kitchen';
    case Bathroom = 'bathroom';
    case GuestBathroom = 'guest_bathroom';
    case Balcony = 'balcony';
    case Terrace = 'terrace';
    case Other = 'other';

    public function label(): string
    {
        return __('enums.image_type.'.$this->value);
    }
}
