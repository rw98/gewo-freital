<?php

use App\Enums\ListingRequestStatus;
use App\Models\Listing;
use App\Models\ListingRequest;
use App\Models\User;
use App\Notifications\StatusChangedNotification;
use App\Services\ListingRequestService;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->service = app(ListingRequestService::class);
});

it('can transition from confirmed to appointment_pending', function () {
    Notification::fake();

    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();
    $employee = User::factory()->create();

    $this->service->transitionStatus($request, ListingRequestStatus::AppointmentPending, $employee);

    expect($request->refresh()->status)->toBe(ListingRequestStatus::AppointmentPending);
    Notification::assertSentOnDemand(StatusChangedNotification::class);
});

it('can transition from confirmed to waiting_for_approval', function () {
    Notification::fake();

    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();
    $employee = User::factory()->create();

    $this->service->transitionStatus($request, ListingRequestStatus::WaitingForApproval, $employee);

    expect($request->refresh()->status)->toBe(ListingRequestStatus::WaitingForApproval);
});

it('can approve a request', function () {
    Notification::fake();

    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->create([
            'status' => ListingRequestStatus::WaitingForApproval,
            'email_confirmed_at' => now(),
        ]);
    $employee = User::factory()->create();

    $this->service->transitionStatus($request, ListingRequestStatus::Approved, $employee);

    $request->refresh();
    expect($request->status)->toBe(ListingRequestStatus::Approved);
    expect($request->approved_at)->not->toBeNull();
    expect($request->approved_by)->toBe($employee->id);
});

it('can reject a request with reason', function () {
    Notification::fake();

    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();
    $employee = User::factory()->create();

    $this->service->transitionStatus(
        $request,
        ListingRequestStatus::Rejected,
        $employee,
        'Bonität nicht ausreichend'
    );

    $request->refresh();
    expect($request->status)->toBe(ListingRequestStatus::Rejected);
    expect($request->rejected_at)->not->toBeNull();
    expect($request->rejection_reason)->toBe('Bonität nicht ausreichend');
});

it('throws exception for invalid transition', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->create(['status' => ListingRequestStatus::Requested]);
    $employee = User::factory()->create();

    $this->service->transitionStatus($request, ListingRequestStatus::Approved, $employee);
})->throws(\InvalidArgumentException::class);

it('can close a signed request', function () {
    Notification::fake();

    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->signed()
        ->create();
    $employee = User::factory()->create();

    $this->service->transitionStatus($request, ListingRequestStatus::Closed, $employee);

    $request->refresh();
    expect($request->status)->toBe(ListingRequestStatus::Closed);
    expect($request->closed_at)->not->toBeNull();
});

it('sends notification for notifiable status like appointment_pending', function () {
    Notification::fake();

    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();
    $employee = User::factory()->create();

    $this->service->transitionStatus($request, ListingRequestStatus::AppointmentPending, $employee);

    // Verify notification is sent and shouldSend returns true for notifiable statuses
    Notification::assertSentOnDemand(
        StatusChangedNotification::class,
        function ($notification, $channels, $notifiable) {
            return $notification->shouldSend($notifiable, 'mail');
        }
    );
});

it('filters notification for non-notifiable status like waiting_for_approval', function () {
    $listing = Listing::factory()->published()->create();
    $request = ListingRequest::factory()
        ->for($listing)
        ->confirmed()
        ->create();

    // Create notification for WaitingForApproval (not in notifiable list)
    $request->status = ListingRequestStatus::WaitingForApproval;
    $notification = new StatusChangedNotification($request);

    // shouldSend should return false for non-notifiable statuses
    expect($notification->shouldSend(new \stdClass, 'mail'))->toBeFalse();

    // shouldSend should return true for notifiable statuses
    $request->status = ListingRequestStatus::Confirmed;
    $notification = new StatusChangedNotification($request);
    expect($notification->shouldSend(new \stdClass, 'mail'))->toBeTrue();
});
