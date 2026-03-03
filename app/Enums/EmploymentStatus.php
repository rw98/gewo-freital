<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case Employed = 'employed';
    case SelfEmployed = 'self_employed';
    case CivilServant = 'civil_servant';
    case Student = 'student';
    case Retired = 'retired';
    case Unemployed = 'unemployed';
    case Apprentice = 'apprentice';
    case Other = 'other';

    public function label(): string
    {
        return __('enums.employment_status.'.$this->value);
    }
}
