<?php

use App\Enums\ListingStatus;
use App\Models\Listing;

it('displays a published listing', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'title' => 'Beautiful Apartment',
    ]);

    $this->get(route('listings.show', $listing))
        ->assertSuccessful()
        ->assertSee('Beautiful Apartment');
});

it('returns 404 for draft listings', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Draft,
        'title' => 'Draft Apartment',
    ]);

    $this->get(route('listings.show', $listing))
        ->assertNotFound();
});

it('returns 404 for archived listings', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Archived,
        'title' => 'Archived Apartment',
    ]);

    $this->get(route('listings.show', $listing))
        ->assertNotFound();
});

it('displays listing details', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'title' => 'Test Apartment',
        'description' => 'A wonderful apartment',
        'rooms' => 3,
        'size_sqm' => 75.50,
        'rent_cold' => 450.00,
        'utility_cost' => 120.00,
        'city' => 'Freital',
        'street' => 'Teststraße',
        'street_number' => '42',
    ]);

    $this->get(route('listings.show', $listing))
        ->assertSuccessful()
        ->assertSee('Test Apartment')
        ->assertSee('A wonderful apartment')
        ->assertSee('Freital')
        ->assertSee('Teststraße');
});

it('shows similar listings from the same city', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'city' => 'Freital',
        'title' => 'Main Listing',
    ]);

    $similar = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'city' => 'Freital',
        'title' => 'Similar Listing',
    ]);

    $different = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'city' => 'Dresden',
        'title' => 'Different City Listing',
    ]);

    $this->get(route('listings.show', $listing))
        ->assertSuccessful()
        ->assertSee('Similar Listing')
        ->assertDontSee('Different City Listing');
});
