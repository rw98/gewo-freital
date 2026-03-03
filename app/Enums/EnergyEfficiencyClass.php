<?php

namespace App\Enums;

enum EnergyEfficiencyClass: string
{
    case APlus = 'A+';
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case H = 'H';

    /**
     * Get the energy efficiency class for a given kWh/m²a value.
     */
    public static function fromKwh(float $kwh): self
    {
        return match (true) {
            $kwh <= 30 => self::APlus,
            $kwh <= 50 => self::A,
            $kwh <= 75 => self::B,
            $kwh <= 100 => self::C,
            $kwh <= 130 => self::D,
            $kwh <= 160 => self::E,
            $kwh <= 200 => self::F,
            $kwh <= 250 => self::G,
            default => self::H,
        };
    }

    /**
     * Get the kWh/m²a range for this class.
     *
     * @return array{min: int, max: int|null}
     */
    public function range(): array
    {
        return match ($this) {
            self::APlus => ['min' => 0, 'max' => 30],
            self::A => ['min' => 30, 'max' => 50],
            self::B => ['min' => 50, 'max' => 75],
            self::C => ['min' => 75, 'max' => 100],
            self::D => ['min' => 100, 'max' => 130],
            self::E => ['min' => 130, 'max' => 160],
            self::F => ['min' => 160, 'max' => 200],
            self::G => ['min' => 200, 'max' => 250],
            self::H => ['min' => 250, 'max' => null],
        };
    }

    /**
     * Get the display label for this class.
     */
    public function label(): string
    {
        return __('enums.energy_efficiency_class.'.$this->value);
    }

    /**
     * Get the color for this class (Tailwind CSS colors).
     */
    public function color(): string
    {
        return match ($this) {
            self::APlus => 'emerald',
            self::A => 'green',
            self::B => 'lime',
            self::C => 'yellow',
            self::D => 'amber',
            self::E => 'orange',
            self::F => 'red',
            self::G => 'red',
            self::H => 'red',
        };
    }

    /**
     * Get the hex color for this class (for charts/visualizations).
     */
    public function hexColor(): string
    {
        return match ($this) {
            self::APlus => '#047857', // emerald-700
            self::A => '#15803d', // green-700
            self::B => '#65a30d', // lime-600
            self::C => '#ca8a04', // yellow-600
            self::D => '#d97706', // amber-600
            self::E => '#ea580c', // orange-600
            self::F => '#dc2626', // red-600
            self::G => '#b91c1c', // red-700
            self::H => '#991b1b', // red-800
        };
    }

    /**
     * Get the position percentage on the scale (0-100).
     */
    public function scalePosition(): int
    {
        return match ($this) {
            self::APlus => 0,
            self::A => 12,
            self::B => 24,
            self::C => 36,
            self::D => 48,
            self::E => 60,
            self::F => 72,
            self::G => 84,
            self::H => 96,
        };
    }

    /**
     * Get a description of typical buildings in this class.
     */
    public function description(): string
    {
        return __('enums.energy_efficiency_description.'.$this->value);
    }
}
