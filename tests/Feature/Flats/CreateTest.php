<?php

use App\Livewire\Flats\Create;
use App\Models\RentalObject;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $rentalObject = RentalObject::factory()->create();

    $this->get(route('flats.create', $rentalObject))
        ->assertRedirect(route('login'));
});

test('owners can view create form', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    $this->actingAs($user)
        ->get(route('flats.create', $rentalObject))
        ->assertOk();
});

test('managers can view create form', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();

    $this->actingAs($user)
        ->get(route('flats.create', $rentalObject))
        ->assertOk();
});

test('caretakers cannot create flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();

    $this->actingAs($user)
        ->get(route('flats.create', $rentalObject))
        ->assertForbidden();
});

test('owners can create flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    $this->actingAs($user);

    Livewire::test(Create::class, ['rentalObject' => $rentalObject])
        ->set('number', '1A')
        ->set('floor', 2)
        ->set('size_sqm', 75.5)
        ->set('rent_cold', 800)
        ->set('utility_cost', 150)
        ->set('is_wheelchair_accessible', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseHas('flats', [
        'rental_object_id' => $rentalObject->id,
        'number' => '1A',
        'floor' => 2,
        'size_sqm' => 75.5,
        'rent_cold' => 800,
        'utility_cost' => 150,
        'is_wheelchair_accessible' => true,
    ]);
});

test('validation errors are shown for invalid data', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    $this->actingAs($user);

    Livewire::test(Create::class, ['rentalObject' => $rentalObject])
        ->set('number', '')
        ->set('size_sqm', '')
        ->set('rent_cold', '')
        ->set('utility_cost', '')
        ->call('save')
        ->assertHasErrors(['number', 'size_sqm', 'rent_cold', 'utility_cost']);
});
