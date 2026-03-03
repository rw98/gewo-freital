<?php

use App\Enums\EnergyEfficiencyClass;

it('calculates correct class from kwh value', function (float $kwh, EnergyEfficiencyClass $expected) {
    expect(EnergyEfficiencyClass::fromKwh($kwh))->toBe($expected);
})->with([
    [0, EnergyEfficiencyClass::APlus],
    [15, EnergyEfficiencyClass::APlus],
    [30, EnergyEfficiencyClass::APlus],
    [31, EnergyEfficiencyClass::A],
    [50, EnergyEfficiencyClass::A],
    [51, EnergyEfficiencyClass::B],
    [75, EnergyEfficiencyClass::B],
    [76, EnergyEfficiencyClass::C],
    [100, EnergyEfficiencyClass::C],
    [101, EnergyEfficiencyClass::D],
    [130, EnergyEfficiencyClass::D],
    [131, EnergyEfficiencyClass::E],
    [160, EnergyEfficiencyClass::E],
    [161, EnergyEfficiencyClass::F],
    [200, EnergyEfficiencyClass::F],
    [201, EnergyEfficiencyClass::G],
    [250, EnergyEfficiencyClass::G],
    [251, EnergyEfficiencyClass::H],
    [500, EnergyEfficiencyClass::H],
]);

it('provides correct range for each class', function () {
    expect(EnergyEfficiencyClass::APlus->range())->toBe(['min' => 0, 'max' => 30]);
    expect(EnergyEfficiencyClass::A->range())->toBe(['min' => 30, 'max' => 50]);
    expect(EnergyEfficiencyClass::H->range())->toBe(['min' => 250, 'max' => null]);
});

it('provides hex color for each class', function () {
    expect(EnergyEfficiencyClass::APlus->hexColor())->toStartWith('#');
    expect(EnergyEfficiencyClass::H->hexColor())->toStartWith('#');
});

it('provides scale position for each class', function () {
    expect(EnergyEfficiencyClass::APlus->scalePosition())->toBe(0);
    expect(EnergyEfficiencyClass::H->scalePosition())->toBe(96);
});
