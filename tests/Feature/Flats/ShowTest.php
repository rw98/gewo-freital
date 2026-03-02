<?php

use App\Models\Flat;
use App\Models\RentalObject;
use App\Models\User;

test('guests are redirected to login', function () {
    $flat = Flat::factory()->create();

    $this->get(route('flats.show', $flat))
        ->assertRedirect(route('login'));
});

test('contacts can view flat', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    $this->actingAs($user)
        ->get(route('flats.show', $flat))
        ->assertOk()
        ->assertSee($flat->number);
});

test('tenants can view their flat', function () {
    $user = User::factory()->create();
    $flat = Flat::factory()->withTenant($user)->create();

    $this->actingAs($user)
        ->get(route('flats.show', $flat))
        ->assertOk();
});

test('non-contacts and non-tenants cannot view flat', function () {
    $user = User::factory()->create();
    $flat = Flat::factory()->create();

    $this->actingAs($user)
        ->get(route('flats.show', $flat))
        ->assertForbidden();
});
