<?php

use App\Livewire\Flats\DeleteForm;
use App\Models\Flat;
use App\Models\RentalObject;
use App\Models\User;
use Livewire\Livewire;

test('owners can delete flat', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    $this->actingAs($user);

    Livewire::test(DeleteForm::class, ['flat' => $flat])
        ->call('delete')
        ->assertRedirect(route('rental-objects.show', $rentalObject));

    $this->assertDatabaseMissing('flats', [
        'id' => $flat->id,
    ]);
});

test('managers cannot delete flat', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    $this->actingAs($user);

    Livewire::test(DeleteForm::class, ['flat' => $flat])
        ->call('delete')
        ->assertForbidden();

    $this->assertDatabaseHas('flats', [
        'id' => $flat->id,
    ]);
});

test('caretakers cannot delete flat', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    $this->actingAs($user);

    Livewire::test(DeleteForm::class, ['flat' => $flat])
        ->call('delete')
        ->assertForbidden();

    $this->assertDatabaseHas('flats', [
        'id' => $flat->id,
    ]);
});
