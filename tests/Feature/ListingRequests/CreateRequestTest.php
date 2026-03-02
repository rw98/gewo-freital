<?php

use App\Enums\ListingRequestStatus;
use App\Enums\ListingStatus;
use App\Livewire\ListingRequests\Create;
use App\Models\Listing;
use App\Models\ListingRequest;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\RequestReceivedNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

it('shows the request form for a published listing', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
        'title' => 'Test Apartment',
    ]);

    $this->get(route('listing-requests.create', $listing))
        ->assertSuccessful()
        ->assertSee('Test Apartment');
});

it('returns 404 for draft listings', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Draft,
    ]);

    $this->get(route('listing-requests.create', $listing))
        ->assertNotFound();
});

it('can submit a request with valid data', function () {
    Notification::fake();

    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
    ]);

    Livewire::test(Create::class, ['listing' => $listing])
        ->set('first_name', 'Max')
        ->set('last_name', 'Mustermann')
        ->set('email', 'max@example.com')
        ->set('phone', '+49 123 456789')
        ->set('message', 'I am interested in this apartment.')
        ->call('submit')
        ->assertSet('submitted', true);

    $this->assertDatabaseHas('listing_requests', [
        'listing_id' => $listing->id,
        'first_name' => 'Max',
        'last_name' => 'Mustermann',
        'email' => 'max@example.com',
        'status' => ListingRequestStatus::PendingEmailConfirmation->value,
    ]);

    Notification::assertSentOnDemand(RequestReceivedNotification::class);
    Notification::assertSentOnDemand(EmailVerificationNotification::class);
});

it('validates required fields', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
    ]);

    Livewire::test(Create::class, ['listing' => $listing])
        ->set('first_name', '')
        ->set('last_name', '')
        ->set('email', '')
        ->call('submit')
        ->assertHasErrors(['first_name', 'last_name', 'email']);
});

it('validates email format', function () {
    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
    ]);

    Livewire::test(Create::class, ['listing' => $listing])
        ->set('first_name', 'Max')
        ->set('last_name', 'Mustermann')
        ->set('email', 'invalid-email')
        ->call('submit')
        ->assertHasErrors(['email']);
});

it('generates unique access token', function () {
    Notification::fake();

    $listing = Listing::factory()->create([
        'status' => ListingStatus::Published,
        'published_at' => now(),
    ]);

    Livewire::test(Create::class, ['listing' => $listing])
        ->set('first_name', 'Max')
        ->set('last_name', 'Mustermann')
        ->set('email', 'max@example.com')
        ->call('submit');

    $request = ListingRequest::first();
    expect($request->access_token)->toHaveLength(64);
});
