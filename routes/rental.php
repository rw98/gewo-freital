<?php

use App\Livewire\Flats;
use App\Livewire\ListingRequests;
use App\Livewire\RentalObjects;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rental Objects
    Route::livewire('rental-objects', RentalObjects\Index::class)->name('rental-objects.index');
    Route::livewire('rental-objects/create', RentalObjects\Create::class)->name('rental-objects.create');
    Route::livewire('rental-objects/{rentalObject}', RentalObjects\Show::class)->name('rental-objects.show');
    Route::livewire('rental-objects/{rentalObject}/edit', RentalObjects\Edit::class)->name('rental-objects.edit');

    // Flats
    Route::livewire('flats', Flats\Index::class)->name('flats.index');
    Route::livewire('rental-objects/{rentalObject}/flats/create', Flats\Create::class)->name('flats.create');
    Route::livewire('flats/{flat}', Flats\Show::class)->name('flats.show');
    Route::livewire('flats/{flat}/edit', Flats\Edit::class)->name('flats.edit');

    // Listing Requests - Employee Management
    Route::livewire('verwaltung/anfragen', ListingRequests\Employee\Index::class)->name('listing-requests.index');
    Route::livewire('verwaltung/anfragen/termine/{listing}', ListingRequests\Employee\Timeslots::class)->name('listing-requests.timeslots');
    Route::livewire('verwaltung/anfragen/{listingRequest}', ListingRequests\Employee\Show::class)->name('listing-requests.show');
});
