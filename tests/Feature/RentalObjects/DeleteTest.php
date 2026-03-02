<?php

use App\Livewire\RentalObjects\DeleteForm;
use App\Models\RentalObject;
use App\Models\User;
use Livewire\Livewire;

test('owners can delete rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    $this->actingAs($user);

    Livewire::test(DeleteForm::class, ['rentalObject' => $rentalObject])
        ->call('delete')
        ->assertRedirect(route('rental-objects.index'));

    $this->assertDatabaseMissing('rental_objects', [
        'id' => $rentalObject->id,
    ]);
});

test('managers cannot delete rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();

    $this->actingAs($user);

    Livewire::test(DeleteForm::class, ['rentalObject' => $rentalObject])
        ->call('delete')
        ->assertForbidden();

    $this->assertDatabaseHas('rental_objects', [
        'id' => $rentalObject->id,
    ]);
});

test('caretakers cannot delete rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();

    $this->actingAs($user);

    Livewire::test(DeleteForm::class, ['rentalObject' => $rentalObject])
        ->call('delete')
        ->assertForbidden();

    $this->assertDatabaseHas('rental_objects', [
        'id' => $rentalObject->id,
    ]);
});
