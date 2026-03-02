<?php

use App\Livewire\Flats\Edit;
use App\Models\Flat;
use App\Models\RentalObject;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $flat = Flat::factory()->create();

    $this->get(route('flats.edit', $flat))
        ->assertRedirect(route('login'));
});

test('owners can edit flat', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    $this->actingAs($user)
        ->get(route('flats.edit', $flat))
        ->assertOk();
});

test('managers can edit flat', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    $this->actingAs($user)
        ->get(route('flats.edit', $flat))
        ->assertOk();
});

test('caretakers cannot edit flat', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    $this->actingAs($user)
        ->get(route('flats.edit', $flat))
        ->assertForbidden();
});

test('tenants cannot edit flat', function () {
    $user = User::factory()->create();
    $flat = Flat::factory()->withTenant($user)->create();

    $this->actingAs($user)
        ->get(route('flats.edit', $flat))
        ->assertForbidden();
});

test('owners can update flat', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['flat' => $flat])
        ->set('number', '2B')
        ->set('rent_cold', 1000)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    expect($flat->fresh())
        ->number->toBe('2B')
        ->rent_cold->toBe('1000.00');
});
