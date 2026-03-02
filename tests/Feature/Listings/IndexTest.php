<?php

use App\Enums\ListingStatus;
use App\Models\Listing;
use Livewire\Livewire;

it('displays the listings index page', function () {
    $this->get(route('listings.index'))
        ->assertSuccessful();
});

it('shows only published listings', function () {
    $published = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'title' => 'Published Listing',
    ]);

    $draft = Listing::factory()->create([
        'status' => ListingStatus::Draft,
        'title' => 'Draft Listing',
    ]);

    Livewire::test(\App\Livewire\Listings\Index::class)
        ->assertSee('Published Listing')
        ->assertDontSee('Draft Listing');
});

it('can filter listings by city', function () {
    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'city' => 'Freital',
        'title' => 'Freital Listing',
    ]);

    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'city' => 'Dresden',
        'title' => 'Dresden Listing',
    ]);

    Livewire::test(\App\Livewire\Listings\Index::class)
        ->set('city', 'Freital')
        ->assertSee('Freital Listing')
        ->assertDontSee('Dresden Listing');
});

it('can filter listings by minimum rooms', function () {
    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'rooms' => 2,
        'title' => 'Small Listing',
    ]);

    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'rooms' => 4,
        'title' => 'Large Listing',
    ]);

    Livewire::test(\App\Livewire\Listings\Index::class)
        ->set('minRooms', 3)
        ->assertDontSee('Small Listing')
        ->assertSee('Large Listing');
});

it('can filter listings by max rent', function () {
    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'rent_cold' => 300,
        'utility_cost' => 100,
        'title' => 'Cheap Listing',
    ]);

    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'rent_cold' => 600,
        'utility_cost' => 150,
        'title' => 'Expensive Listing',
    ]);

    Livewire::test(\App\Livewire\Listings\Index::class)
        ->set('maxRent', 500)
        ->assertSee('Cheap Listing')
        ->assertDontSee('Expensive Listing');
});

it('can search listings by title', function () {
    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'title' => 'Wohnung Sonnenseite',
    ]);

    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'title' => 'Apartment Nordblick',
    ]);

    Livewire::test(\App\Livewire\Listings\Index::class)
        ->set('search', 'Sonnenseite')
        ->assertSee('Wohnung Sonnenseite')
        ->assertDontSee('Apartment Nordblick');
});

it('can reset filters', function () {
    Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'city' => 'Freital',
        'title' => 'Test Listing',
    ]);

    Livewire::test(\App\Livewire\Listings\Index::class)
        ->set('city', 'Dresden')
        ->assertDontSee('Test Listing')
        ->call('resetFilters')
        ->assertSee('Test Listing');
});
