<?php

use App\Enums\ListingStatus;
use App\Livewire\Landing\ApartmentSearch;
use App\Models\Listing;
use Livewire\Livewire;

it('displays the apartment search component', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSeeLivewire(ApartmentSearch::class);
});

it('shows featured listings', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'city' => 'Freital',
        'rooms' => 3,
        'size_sqm' => 75,
    ]);

    Livewire::test(ApartmentSearch::class)
        ->assertSee('Freital')
        ->assertSee('3');
});

it('does not show draft listings', function () {
    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'city' => 'Freital',
    ]);

    Listing::factory()->create([
        'status' => ListingStatus::Draft,
        'city' => 'Dresden-Hidden',
    ]);

    Livewire::test(ApartmentSearch::class)
        ->assertSee('Freital')
        ->assertDontSee('Dresden-Hidden');
});

it('redirects to listings page with filters on search', function () {
    Livewire::test(ApartmentSearch::class)
        ->set('city', 'Freital')
        ->set('minRooms', '3')
        ->set('maxRent', 500)
        ->call('search')
        ->assertRedirect(route('listings.index', [
            'city' => 'Freital',
            'minRooms' => '3',
            'maxRent' => 500,
        ]));
});

it('shows total count when more than 3 listings exist', function () {
    Listing::factory()->count(5)->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
    ]);

    Livewire::test(ApartmentSearch::class)
        ->assertSee('(5)');
});
