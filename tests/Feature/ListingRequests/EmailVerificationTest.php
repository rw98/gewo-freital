<?php

use App\Enums\ListingRequestStatus;
use App\Models\Listing;
use App\Models\ListingRequest;
use Illuminate\Support\Facades\URL;

it('confirms email with valid signed url', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->pendingEmailConfirmation()
        ->create();

    $url = URL::temporarySignedRoute(
        'listing-requests.verify-email',
        now()->addHours(24),
        ['listingRequest' => $request->id]
    );

    $this->get($url)
        ->assertRedirect(route('listing-requests.portal', $request->access_token));

    $request->refresh();
    expect($request->status)->toBe(ListingRequestStatus::Confirmed);
    expect($request->email_confirmed_at)->not->toBeNull();
});

it('returns 403 for invalid signature', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->pendingEmailConfirmation()
        ->create();

    $url = route('listing-requests.verify-email', ['listingRequest' => $request->id]);

    $this->get($url)
        ->assertForbidden();
});

it('returns 403 for expired signature', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->pendingEmailConfirmation()
        ->create();

    $url = URL::temporarySignedRoute(
        'listing-requests.verify-email',
        now()->subHour(),
        ['listingRequest' => $request->id]
    );

    $this->get($url)
        ->assertForbidden();
});

it('does not change status if already confirmed', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();

    $url = URL::temporarySignedRoute(
        'listing-requests.verify-email',
        now()->addHours(24),
        ['listingRequest' => $request->id]
    );

    $this->get($url)
        ->assertRedirect(route('listing-requests.portal', $request->access_token));

    $request->refresh();
    expect($request->status)->toBe(ListingRequestStatus::Confirmed);
});
