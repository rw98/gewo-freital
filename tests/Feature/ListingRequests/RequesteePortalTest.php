<?php

use App\Livewire\ListingRequests\Requestee\Appointments;
use App\Livewire\ListingRequests\Requestee\Messages;
use App\Models\Listing;
use App\Models\ListingRequest;
use App\Models\RequestTimeslot;
use Livewire\Livewire;

it('shows the requestee portal with valid access token', function () {
    $listing = Listing::factory()->published()->create(['title' => 'Test Apartment']);
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();

    $this->get(route('listing-requests.portal', $request->access_token))
        ->assertSuccessful()
        ->assertSee('Test Apartment');
});

it('returns 404 for invalid access token', function () {
    $this->get(route('listing-requests.portal', 'invalid-token'))
        ->assertNotFound();
});

it('shows documents page', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();

    $this->get(route('listing-requests.documents', $request->access_token))
        ->assertSuccessful();
});

it('shows appointments page', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();

    $this->get(route('listing-requests.appointments', $request->access_token))
        ->assertSuccessful();
});

it('shows messages page', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();

    $this->get(route('listing-requests.messages', $request->access_token))
        ->assertSuccessful();
});

it('can send a message from the portal', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();

    Livewire::test(Messages::class, ['access_token' => $request->access_token])
        ->set('content', 'Hello, I have a question.')
        ->call('send');

    $this->assertDatabaseHas('request_messages', [
        'listing_request_id' => $request->id,
        'sender_type' => 'requestee',
        'content' => 'Hello, I have a question.',
    ]);
});

it('can book an appointment', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();

    $timeslot = RequestTimeslot::factory()
        ->for($listing)
        ->create([
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHour(),
            'max_attendees' => 5,
        ]);

    Livewire::test(Appointments::class, ['access_token' => $request->access_token])
        ->call('book', $timeslot->id);

    $this->assertDatabaseHas('request_appointments', [
        'listing_request_id' => $request->id,
        'timeslot_id' => $timeslot->id,
        'status' => 'pending',
    ]);
});
