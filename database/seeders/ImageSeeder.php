<?php

namespace Database\Seeders;

use App\Enums\ImageType;
use App\Models\Flat;
use App\Models\RentalObject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageSeeder extends Seeder
{
    /**
     * Image types for rental objects.
     *
     * @var array<int, ImageType>
     */
    protected array $rentalObjectTypes = [
        ImageType::Exterior,
        ImageType::Entrance,
        ImageType::Interior,
    ];

    /**
     * Image types for flats.
     *
     * @var array<int, ImageType>
     */
    protected array $flatTypes = [
        ImageType::LivingRoom,
        ImageType::Kitchen,
        ImageType::Bedroom,
        ImageType::Bathroom,
        ImageType::Layout,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('images/rental_objects');
        Storage::disk('public')->makeDirectory('images/flats');

        $this->seedRentalObjectImages();
        $this->seedFlatImages();
    }

    protected function seedRentalObjectImages(): void
    {
        $rentalObjects = RentalObject::all();

        foreach ($rentalObjects as $rentalObject) {
            $imageCount = rand(2, 4);

            for ($i = 0; $i < $imageCount; $i++) {
                $type = $this->rentalObjectTypes[array_rand($this->rentalObjectTypes)];
                $this->createImage($rentalObject, $type, 'rental_objects');
            }
        }
    }

    protected function seedFlatImages(): void
    {
        $flats = Flat::inRandomOrder()->limit(30)->get();

        foreach ($flats as $flat) {
            $imageCount = rand(2, 5);
            $shuffledTypes = $this->flatTypes;
            shuffle($shuffledTypes);

            for ($i = 0; $i < $imageCount; $i++) {
                $type = $shuffledTypes[$i % count($shuffledTypes)];
                $this->createImage($flat, $type, 'flats');
            }
        }
    }

    protected function createImage($model, ImageType $type, string $folder): void
    {
        $width = $type === ImageType::Layout ? 800 : 1200;
        $height = $type === ImageType::Layout ? 600 : 800;

        $imageContent = $this->downloadPlaceholderImage($width, $height);

        if ($imageContent === null) {
            return;
        }

        $filename = Str::uuid().'.jpg';
        $path = "images/{$folder}/{$filename}";

        Storage::disk('public')->put($path, $imageContent);

        $model->images()->create([
            'type' => $type,
            'path' => $path,
            'filename' => $type->value.'_'.Str::random(8).'.jpg',
            'order' => $model->images()->count(),
        ]);
    }

    protected function downloadPlaceholderImage(int $width, int $height): ?string
    {
        try {
            $response = Http::timeout(10)->get("https://picsum.photos/{$width}/{$height}");

            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Exception $e) {
            $this->command?->warn("Could not download placeholder image: {$e->getMessage()}");
        }

        return null;
    }
}
