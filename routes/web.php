<?php

use App\Http\Controllers\ListingRequestController;
use App\Livewire\Forms;
use App\Livewire\ListingRequests;
use App\Livewire\Listings;
use App\Livewire\Pages;
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
    Route::livewire('/formular', ListingRequests\Requestee\CustomForm::class)->name('custom-form');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// CMS Pages - Admin
Route::middleware(['auth'])->prefix('admin/seiten')->name('pages.')->group(function () {
    Route::livewire('/', Pages\Index::class)->name('index');
    Route::livewire('/erstellen', Pages\Create::class)->name('create');
    Route::livewire('/{page}/bearbeiten', Pages\Builder::class)->name('builder');
});

// Forms - Admin
Route::middleware(['auth'])->prefix('admin/formulare')->name('forms.')->group(function () {
    Route::livewire('/', Forms\Index::class)->name('index');
    Route::livewire('/{form}/bearbeiten', Forms\Builder::class)->name('builder');
});

// Forms - Public
Route::livewire('/f/{key}', Forms\PublicForm::class)->name('forms.public');

// CMS Pages - Public
Route::livewire('/p/{slug}', Pages\PublicShow::class)->name('pages.show');

require __DIR__.'/settings.php';
require __DIR__.'/rental.php';
