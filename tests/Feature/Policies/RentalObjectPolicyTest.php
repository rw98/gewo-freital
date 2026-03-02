<?php

use App\Models\RentalObject;
use App\Models\User;

test('any authenticated user can view any rental objects', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', RentalObject::class))->toBeTrue();
});

test('any authenticated user can create rental objects', function () {
    $user = User::factory()->create();

    expect($user->can('create', RentalObject::class))->toBeTrue();
});

test('owners can view their rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    expect($user->can('view', $rentalObject))->toBeTrue();
});

test('managers can view their rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();

    expect($user->can('view', $rentalObject))->toBeTrue();
});

test('caretakers can view their rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();

    expect($user->can('view', $rentalObject))->toBeTrue();
});

test('non-contacts cannot view rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->create();

    expect($user->can('view', $rentalObject))->toBeFalse();
});

test('owners can update their rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    expect($user->can('update', $rentalObject))->toBeTrue();
});

test('managers can update their rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();

    expect($user->can('update', $rentalObject))->toBeTrue();
});

test('caretakers cannot update rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();

    expect($user->can('update', $rentalObject))->toBeFalse();
});

test('owners can delete their rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    expect($user->can('delete', $rentalObject))->toBeTrue();
});

test('managers cannot delete rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();

    expect($user->can('delete', $rentalObject))->toBeFalse();
});

test('caretakers cannot delete rental objects', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();

    expect($user->can('delete', $rentalObject))->toBeFalse();
});
