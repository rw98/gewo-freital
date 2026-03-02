<?php

namespace App\Enums;

enum RequestAppointmentStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return __('enums.request_appointment_status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Confirmed => 'green',
            self::Cancelled => 'red',
            self::Completed => 'slate',
        };
    }
}
