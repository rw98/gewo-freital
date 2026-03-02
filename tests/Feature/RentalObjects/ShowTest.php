<?php

use App\Models\RentalObject;
use App\Models\User;

test('guests are redirected to login', function () {
    $rentalObject = RentalObject::factory()->create();

    $this->get(route('rental-objects.show', $rentalObject))
        ->assertRedirect(route('login'));
});

test('contacts can view rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    $this->actingAs($user)
        ->get(route('rental-objects.show', $rentalObject))
        ->assertOk()
        ->assertSee($rentalObject->street);
});

test('non-contacts cannot view rental object', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($otherUser)->create();

    $this->actingAs($user)
        ->get(route('rental-objects.show', $rentalObject))
        ->assertForbidden();
});

test('caretakers can view rental object', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();

    $this->actingAs($user)
        ->get(route('rental-objects.show', $rentalObject))
        ->assertOk();
});
