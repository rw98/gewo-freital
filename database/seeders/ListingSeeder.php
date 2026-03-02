<?php

namespace Database\Seeders;

use App\Models\Flat;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', config('app.admin_email'))->first();

        // Get random flats without existing tenants for listings
        $flats = Flat::inRandomOrder()->limit(15)->get();

        foreach ($flats as $index => $flat) {
            $listing = Listing::createFromFlat($flat, [
                'created_by' => $admin?->id,
                'description' => $this->generateDescription($flat),
                'available_from' => now()->addDays(rand(7, 60)),
                'pets_allowed' => rand(0, 10) > 3 ? true : (rand(0, 10) > 7 ? false : null),
                'amenities' => $this->randomAmenities(),
            ]);

            // Publish some listings
            if ($index < 10) {
                $listing->update([
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }

    protected function generateDescription(Flat $flat): string
    {
        $descriptions = [
            'Gemütliche Wohnung in ruhiger Lage mit guter Verkehrsanbindung.',
            'Helle und freundliche Wohnung mit modernem Schnitt.',
            'Frisch renovierte Wohnung mit hochwertiger Ausstattung.',
            'Zentral gelegene Wohnung mit Balkon und Einbauküche.',
            'Familienfreundliche Wohnung in gepflegtem Mehrfamilienhaus.',
            'Attraktive Wohnung mit Blick ins Grüne.',
            'Schöne Wohnung in beliebter Wohnlage.',
        ];

        $base = $descriptions[array_rand($descriptions)];

        $features = [];
        if ($flat->is_wheelchair_accessible) {
            $features[] = 'Barrierefrei zugänglich.';
        }
        if ($flat->rentalObject->has_elevator) {
            $features[] = 'Aufzug vorhanden.';
        }
        if ($flat->outdoorSpaces()->exists()) {
            $outdoorType = $flat->outdoorSpaces()->first()->type->value === 'balcony' ? 'Balkon' : 'Terrasse';
            $features[] = "Mit {$outdoorType}.";
        }

        return $base.' '.implode(' ', $features);
    }

    /**
     * @return array<int, string>
     */
    protected function randomAmenities(): array
    {
        $all = [
            'Keller',
            'Stellplatz',
            'Einbauküche',
            'Fußbodenheizung',
            'Garten',
            'Waschküche',
            'Fahrradkeller',
            'Abstellraum',
        ];

        shuffle($all);

        return array_slice($all, 0, rand(0, 4));
    }
}
