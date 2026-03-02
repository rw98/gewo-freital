<?php

use App\Livewire\RentalObjects\Create;
use App\Models\RentalObject;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('rental-objects.create'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view create form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('rental-objects.create'))
        ->assertOk();
});

test('users can create rental objects', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('object_number', 'TEST-001')
        ->set('street', 'Hauptstraße')
        ->set('number', '123')
        ->set('city', 'Berlin')
        ->set('postal_code', '10115')
        ->set('country', 'DE')
        ->set('has_elevator', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseHas('rental_objects', [
        'object_number' => 'TEST-001',
        'street' => 'Hauptstraße',
        'number' => '123',
        'city' => 'Berlin',
        'postal_code' => '10115',
        'country' => 'DE',
        'has_elevator' => true,
    ]);

    $rentalObject = RentalObject::first();
    expect($rentalObject->contacts()->where('user_id', $user->id)->first()->pivot->role)
        ->toBe('owner');
});

test('validation errors are shown for invalid data', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('object_number', '')
        ->set('street', '')
        ->set('number', '')
        ->set('city', '')
        ->set('postal_code', '')
        ->call('save')
        ->assertHasErrors(['object_number', 'street', 'number', 'city', 'postal_code']);
});
