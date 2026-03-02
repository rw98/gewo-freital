<?php

use App\Models\Flat;
use App\Models\RentalObject;
use App\Models\User;

test('any authenticated user can view any flats', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Flat::class))->toBeTrue();
});

test('contacts can view flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    expect($user->can('view', $flat))->toBeTrue();
});

test('tenants can view their flats', function () {
    $user = User::factory()->create();
    $flat = Flat::factory()->withTenant($user)->create();

    expect($user->can('view', $flat))->toBeTrue();
});

test('non-contacts and non-tenants cannot view flats', function () {
    $user = User::factory()->create();
    $flat = Flat::factory()->create();

    expect($user->can('view', $flat))->toBeFalse();
});

test('owners can create flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();

    expect($user->can('create', [Flat::class, $rentalObject]))->toBeTrue();
});

test('managers can create flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();

    expect($user->can('create', [Flat::class, $rentalObject]))->toBeTrue();
});

test('caretakers cannot create flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();

    expect($user->can('create', [Flat::class, $rentalObject]))->toBeFalse();
});

test('owners can update flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    expect($user->can('update', $flat))->toBeTrue();
});

test('managers can update flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    expect($user->can('update', $flat))->toBeTrue();
});

test('caretakers cannot update flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withCaretaker($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    expect($user->can('update', $flat))->toBeFalse();
});

test('tenants cannot update flats', function () {
    $user = User::factory()->create();
    $flat = Flat::factory()->withTenant($user)->create();

    expect($user->can('update', $flat))->toBeFalse();
});

test('owners can delete flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withOwner($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    expect($user->can('delete', $flat))->toBeTrue();
});

test('managers cannot delete flats', function () {
    $user = User::factory()->create();
    $rentalObject = RentalObject::factory()->withManager($user)->create();
    $flat = Flat::factory()->forRentalObject($rentalObject)->create();

    expect($user->can('delete', $flat))->toBeFalse();
});
