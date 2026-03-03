<?php

use App\Http\Controllers\ListingRequestController;
use App\Livewire\ListingRequests;
use App\Livewire\Listings;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');

// Public Listings
Route::livewire('wohnungen', Listings\Index::class)->name('listings.index');
Route::livewire('wohnungen/{listing}', Listings\Show::class)->name('listings.show');

// Listing Requests - Public
Route::livewire('wohnungen/{listing}/anfrage', ListingRequests\Create::class)->name('listing-requests.create');
Route::get('anfrage/bestaetigen/{listingRequest}', [ListingRequestController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('listing-requests.verify-email');

// Requestee Portal (token-based access)
Route::prefix('anfrage/{access_token}')->name('listing-requests.')->group(function () {
    Route::livewire('/', ListingRequests\Requestee\Show::class)->name('portal');
    Route::livewire('/dokumente', ListingRequests\Requestee\Documents::class)->name('documents');
    Route::livewire('/termine', ListingRequests\Requestee\Appointments::class)->name('appointments');
    Route::livewire('/nachrichten', ListingRequests\Requestee\Messages::class)->name('messages');
    Route::livewire('/selbstauskunft', ListingRequests\Requestee\SelfDisclosure::class)->name('self-disclosure');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/rental.php';
