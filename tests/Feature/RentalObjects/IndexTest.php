<?php

use App\Livewire\RentalObjects\Index;
use App\Models\RentalObject;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('rental-objects.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('rental-objects.index'))
        ->assertOk();
});

test('users can see only their own rental objects', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $ownRentalObject = RentalObject::factory()->withOwner($user)->create();
    $otherRentalObject = RentalObject::factory()->withOwner($otherUser)->create();

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->assertSee($ownRentalObject->street)
        ->assertDontSee($otherRentalObject->street);
});

test('users can search rental objects', function () {
    $user = User::factory()->create();

    $matchingObject = RentalObject::factory()->withOwner($user)->create([
        'street' => 'Hauptstraße',
        'city' => 'Berlin',
    ]);

    $nonMatchingObject = RentalObject::factory()->withOwner($user)->create([
        'street' => 'Nebenstraße',
        'city' => 'München',
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->set('search', 'Berlin')
        ->assertSee($matchingObject->street)
        ->assertDontSee($nonMatchingObject->street);
});
