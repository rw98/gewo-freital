<?php

namespace Database\Seeders;

use App\Enums\Orientation;
use App\Enums\OutdoorSpaceType;
use App\Models\Flat;
use App\Models\RentalObject;
use App\Models\Room;
use Illuminate\Database\Seeder;

class FlatSeeder extends Seeder
{
    /**
     * Room configurations by flat size.
     *
     * @var array<string, array<int, string>>
     */
    protected array $roomConfigurations = [
        'small' => ['Wohnzimmer', 'Schlafzimmer', 'Küche', 'Bad'],
        'medium' => ['Wohnzimmer', 'Schlafzimmer', 'Kinderzimmer', 'Küche', 'Bad', 'Flur'],
        'large' => ['Wohnzimmer', 'Schlafzimmer', 'Kinderzimmer 1', 'Kinderzimmer 2', 'Küche', 'Bad', 'Gäste-WC', 'Flur', 'Abstellraum'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rentalObjects = RentalObject::all();

        foreach ($rentalObjects as $rentalObject) {
            $this->createFlatsForProperty($rentalObject);
        }
    }

    protected function createFlatsForProperty(RentalObject $rentalObject): void
    {
        $floors = $rentalObject->has_elevator ? rand(4, 8) : rand(2, 4);
        $flatsPerFloor = rand(2, 4);

        for ($floor = 0; $floor < $floors; $floor++) {
            for ($unit = 1; $unit <= $flatsPerFloor; $unit++) {
                $flat = $this->createFlat($rentalObject, $floor, $unit);
                $this->createRoomsForFlat($flat);
                $this->createOutdoorSpacesForFlat($flat);
            }
        }
    }

    protected function createFlat(RentalObject $rentalObject, int $floor, int $unit): Flat
    {
        $sizeCategory = $this->getRandomSizeCategory();
        $sizeRange = $this->getSizeRange($sizeCategory);
        $sizeSqm = fake()->randomFloat(2, $sizeRange['min'], $sizeRange['max']);

        $rentPerSqm = fake()->randomFloat(2, 4.5, 6.5);
        $rentCold = round($sizeSqm * $rentPerSqm, 2);
        $utilityCost = round($sizeSqm * fake()->randomFloat(2, 1.5, 2.5), 2);

        return Flat::create([
            'rental_object_id' => $rentalObject->id,
            'size_sqm' => $sizeSqm,
            'rent_cold' => $rentCold,
            'utility_cost' => $utilityCost,
            'floor' => $floor,
            'number' => sprintf('%d%02d', $floor, $unit),
            'description' => $this->generateDescription($sizeCategory, $floor, $rentalObject->has_elevator),
            'is_wheelchair_accessible' => $floor === 0 && fake()->boolean(40),
        ]);
    }

    protected function createRoomsForFlat(Flat $flat): void
    {
        $sizeCategory = $this->getSizeCategoryFromSqm($flat->size_sqm);
        $rooms = $this->roomConfigurations[$sizeCategory];

        foreach ($rooms as $roomName) {
            Room::create([
                'flat_id' => $flat->id,
                'name' => $roomName,
            ]);
        }
    }

    protected function createOutdoorSpacesForFlat(Flat $flat): void
    {
        if (fake()->boolean(70)) {
            $type = $flat->floor === 0
                ? OutdoorSpaceType::Terrace
                : OutdoorSpaceType::Balcony;

            $flat->outdoorSpaces()->create([
                'type' => $type,
                'orientation' => fake()->randomElement(Orientation::cases()),
                'size_sqm' => $type === OutdoorSpaceType::Terrace
                    ? fake()->randomFloat(2, 15, 40)
                    : fake()->randomFloat(2, 4, 12),
            ]);
        }

        if ($flat->floor === 0 && fake()->boolean(30)) {
            $flat->outdoorSpaces()->create([
                'type' => OutdoorSpaceType::Terrace,
                'orientation' => fake()->randomElement(Orientation::cases()),
                'size_sqm' => fake()->randomFloat(2, 20, 60),
            ]);
        }
    }

    protected function getRandomSizeCategory(): string
    {
        $rand = rand(1, 100);

        if ($rand <= 30) {
            return 'small';
        }

        if ($rand <= 75) {
            return 'medium';
        }

        return 'large';
    }

    /**
     * @return array{min: int, max: int}
     */
    protected function getSizeRange(string $category): array
    {
        return match ($category) {
            'small' => ['min' => 35, 'max' => 55],
            'medium' => ['min' => 55, 'max' => 85],
            'large' => ['min' => 85, 'max' => 130],
        };
    }

    protected function getSizeCategoryFromSqm(float $sqm): string
    {
        if ($sqm < 55) {
            return 'small';
        }

        if ($sqm < 85) {
            return 'medium';
        }

        return 'large';
    }

    protected function generateDescription(string $sizeCategory, int $floor, bool $hasElevator): ?string
    {
        if (fake()->boolean(30)) {
            return null;
        }

        $descriptions = [];

        $descriptions[] = match ($sizeCategory) {
            'small' => 'Gemütliche Wohnung, ideal für Singles oder Paare.',
            'medium' => 'Geräumige Familienwohnung mit durchdachtem Grundriss.',
            'large' => 'Großzügige Wohnung mit viel Platz für die ganze Familie.',
        };

        if ($floor === 0) {
            $descriptions[] = 'Erdgeschoss mit barrierefreiem Zugang.';
        } elseif ($hasElevator) {
            $descriptions[] = 'Bequem erreichbar per Aufzug.';
        }

        if (fake()->boolean(50)) {
            $descriptions[] = fake()->randomElement([
                'Kürzlich renoviert.',
                'Ruhige Lage.',
                'Gute Verkehrsanbindung.',
                'Fußbodenheizung vorhanden.',
                'Moderne Einbauküche.',
                'Tageslichtbad.',
            ]);
        }

        return implode(' ', $descriptions);
    }
}
