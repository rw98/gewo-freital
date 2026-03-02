<?php

namespace Database\Factories;

use App\Enums\ImageType;
use App\Models\Flat;
use App\Models\RentalObject;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->uuid().'.jpg';

        return [
            'imageable_type' => Flat::class,
            'imageable_id' => Flat::factory(),
            'type' => fake()->randomElement(ImageType::cases()),
            'path' => 'images/'.$filename,
            'filename' => $filename,
            'order' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Configure the model factory for a rental object.
     */
    public function forRentalObject(RentalObject $rentalObject): static
    {
        return $this->state(fn (array $attributes) => [
            'imageable_type' => RentalObject::class,
            'imageable_id' => $rentalObject->id,
        ]);
    }

    /**
     * Configure the model factory for a flat.
     */
    public function forFlat(Flat $flat): static
    {
        return $this->state(fn (array $attributes) => [
            'imageable_type' => Flat::class,
            'imageable_id' => $flat->id,
        ]);
    }

    /**
     * Configure the model factory for a room.
     */
    public function forRoom(Room $room): static
    {
        return $this->state(fn (array $attributes) => [
            'imageable_type' => Room::class,
            'imageable_id' => $room->id,
        ]);
    }
}
