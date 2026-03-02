<?php

namespace Database\Seeders;

use App\Models\RentalObject;
use App\Models\User;
use Illuminate\Database\Seeder;

class RentalObjectSeeder extends Seeder
{
    /**
     * Realistic addresses in the Freital/Dresden area.
     *
     * @var array<int, array<string, mixed>>
     */
    protected array $properties = [
        [
            'object_number' => 'FRE-001',
            'street' => 'Dresdner Straße',
            'number' => '84',
            'city' => 'Freital',
            'postal_code' => '01705',
            'has_elevator' => true,
            'year_built' => 1965,
        ],
        [
            'object_number' => 'FRE-002',
            'street' => 'Poisentalstraße',
            'number' => '12',
            'city' => 'Freital',
            'postal_code' => '01705',
            'has_elevator' => false,
            'year_built' => 1954,
        ],
        [
            'object_number' => 'FRE-003',
            'street' => 'Bahnhofstraße',
            'number' => '45',
            'city' => 'Freital',
            'postal_code' => '01705',
            'has_elevator' => true,
            'year_built' => 1972,
        ],
        [
            'object_number' => 'BAN-001',
            'street' => 'Hauptstraße',
            'number' => '23',
            'city' => 'Bannewitz',
            'postal_code' => '01728',
            'has_elevator' => false,
            'year_built' => 1958,
        ],
        [
            'object_number' => 'RAB-001',
            'street' => 'Ringstraße',
            'number' => '8',
            'city' => 'Rabenau',
            'postal_code' => '01734',
            'has_elevator' => false,
            'year_built' => 1962,
        ],
        [
            'object_number' => 'WIL-001',
            'street' => 'Meißner Straße',
            'number' => '102',
            'city' => 'Wilsdruff',
            'postal_code' => '01723',
            'has_elevator' => true,
            'year_built' => 1985,
        ],
        [
            'object_number' => 'DRE-001',
            'street' => 'Coschützer Straße',
            'number' => '17',
            'city' => 'Dresden',
            'postal_code' => '01187',
            'has_elevator' => true,
            'year_built' => 1998,
        ],
        [
            'object_number' => 'DRE-002',
            'street' => 'Altplauen',
            'number' => '5a',
            'city' => 'Dresden',
            'postal_code' => '01187',
            'has_elevator' => false,
            'year_built' => 1924,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', config('app.admin_email'))->first();

        foreach ($this->properties as $property) {
            $rentalObject = RentalObject::create([
                ...$property,
                'country' => 'DE',
            ]);

            if ($admin) {
                $rentalObject->contacts()->attach($admin, ['role' => 'owner']);
            }
        }
    }
}
