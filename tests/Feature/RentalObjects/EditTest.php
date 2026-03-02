<?php

use App\Livewire\RentalObjects\Edit;
use App\Models\RentalObject;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $rentalObject = RentalObject::factory()->create();

    $this->get(route('rental-objects.edit', $rentalObject))
        ->assertRedirect(route('login'));
});

test('owners can edit rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    $this->actingAs($user)
        ->get(route('rental-objects.edit', $rentalObject))
        ->assertOk();
});

test('managers can edit rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();

    $this->actingAs($user)
        ->get(route('rental-objects.edit', $rentalObject))
        ->assertOk();
});

test('caretakers cannot edit rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();

    $this->actingAs($user)
        ->get(route('rental-objects.edit', $rentalObject))
        ->assertForbidden();
});

test('owners can update rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['rentalObject' => $rentalObject])
        ->set('street', 'New Street')
        ->set('city', 'New City')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    expect($rentalObject->fresh())
        ->street->toBe('New Street')
        ->city->toBe('New City');
});

test('non-contacts cannot edit rental object', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($otherUser)->create();

    $this->actingAs($user)
        ->get(route('rental-objects.edit', $rentalObject))
        ->assertForbidden();
});
