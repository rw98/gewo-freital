<?php

use App\Enums\ListingRequestStatus;
use App\Enums\RequestAppointmentStatus;
use App\Models\Listing;
use App\Models\ListingRequest;
use App\Models\RequestAppointment;
use App\Models\RequestTimeslot;
use App\Models\User;
use App\Notifications\AppointmentConfirmedNotification;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\RequestReceivedNotification;
use App\Notifications\StatusChangedNotification;
use App\Services\ICalService;
use App\Services\ListingRequestService;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    Notification::fake();
});

describe('request submission', function () {
    it('can submit a request for a published listing', function () {
        $listing = Listing::factory()->published()->create();

        Livewire::test(\App\Livewire\ListingRequests\Create::class, ['listing' => $listing])
            ->set('email', 'test@example.com')
            ->set('first_name', 'Max')
            ->set('last_name', 'Mustermann')
            ->set('phone', '+49123456789')
            ->set('message', 'Ich bin interessiert an der Wohnung.')
            ->call('submit')
            ->assertSet('submitted', true);

        $request = ListingRequest::first();
        expect($request->email)->toBe('test@example.com');
        expect($request->status)->toBe(ListingRequestStatus::PendingEmailConfirmation);

        Notification::assertSentOnDemand(RequestReceivedNotification::class);
        Notification::assertSentOnDemand(EmailVerificationNotification::class);
    });

    it('cannot submit a request for a draft listing', function () {
        $listing = Listing::factory()->create(['status' => 'draft']);

        $this->get(route('listing-requests.create', $listing))
            ->assertNotFound();
    });
});

describe('email verification', function () {
    it('confirms email and transitions status', function () {
        $request = ListingRequest::factory()
            ->pendingEmailConfirmation()
            ->create();

        $this->get(\Illuminate\Support\Facades\URL::temporarySignedRoute(
            'listing-requests.verify-email',
            now()->addHours(24),
            ['listingRequest' => $request->id]
        ))->assertRedirect();

        $request->refresh();
        expect($request->status)->toBe(ListingRequestStatus::Confirmed);
        expect($request->email_confirmed_at)->not->toBeNull();
    });

    it('rejects invalid signature', function () {
        $request = ListingRequest::factory()
            ->pendingEmailConfirmation()
            ->create();

        $this->get(route('listing-requests.verify-email', $request))
            ->assertForbidden();
    });
});

describe('requestee portal', function () {
    it('can access portal with valid token', function () {
        $request = ListingRequest::factory()->confirmed()->create();

        $this->get(route('listing-requests.portal', $request->access_token))
            ->assertOk()
            ->assertSeeLivewire(\App\Livewire\ListingRequests\Requestee\Show::class);
    });

    it('cannot access portal with invalid token', function () {
        $this->get(route('listing-requests.portal', 'invalid-token'))
            ->assertNotFound();
    });
});

describe('status transitions', function () {
    it('allows valid transitions', function () {
        $employee = User::factory()->create(['is_admin' => true]);
        $request = ListingRequest::factory()->confirmed()->create();

        $service = app(ListingRequestService::class);
        $service->transitionStatus($request, ListingRequestStatus::AppointmentPending, $employee);

        expect($request->refresh()->status)->toBe(ListingRequestStatus::AppointmentPending);
        Notification::assertSentOnDemand(StatusChangedNotification::class);
    });

    it('rejects invalid transitions', function () {
        $employee = User::factory()->create(['is_admin' => true]);
        $request = ListingRequest::factory()->confirmed()->create();

        expect(fn () => app(ListingRequestService::class)->transitionStatus(
            $request,
            ListingRequestStatus::Signed,
            $employee
        ))->toThrow(InvalidArgumentException::class);
    });

    it('sets approved_by and approved_at when approving', function () {
        $employee = User::factory()->create(['is_admin' => true]);
        $request = ListingRequest::factory()->create([
            'status' => ListingRequestStatus::WaitingForApproval,
            'email_confirmed_at' => now(),
        ]);

        app(ListingRequestService::class)->transitionStatus(
            $request,
            ListingRequestStatus::Approved,
            $employee
        );

        $request->refresh();
        expect($request->approved_by)->toBe($employee->id);
        expect($request->approved_at)->not->toBeNull();
    });

    it('sets rejection_reason when rejecting', function () {
        $employee = User::factory()->create(['is_admin' => true]);
        $request = ListingRequest::factory()->confirmed()->create();

        app(ListingRequestService::class)->transitionStatus(
            $request,
            ListingRequestStatus::Rejected,
            $employee,
            'Insufficient income'
        );

        $request->refresh();
        expect($request->rejection_reason)->toBe('Insufficient income');
        expect($request->rejected_at)->not->toBeNull();
    });
});

describe('appointments', function () {
    it('can book an appointment', function () {
        $request = ListingRequest::factory()->confirmed()->create();
        $timeslot = RequestTimeslot::factory()->create([
            'listing_id' => $request->listing_id,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHour(),
            'max_attendees' => 5,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\ListingRequests\Requestee\Appointments::class, [
            'access_token' => $request->access_token,
        ])
            ->call('book', $timeslot);

        expect($request->appointments()->count())->toBe(1);
        expect($request->appointments->first()->status)->toBe(RequestAppointmentStatus::Pending);
        Notification::assertSentOnDemand(AppointmentConfirmedNotification::class);
    });

    it('prevents double-booking when already has active appointment', function () {
        $request = ListingRequest::factory()->confirmed()->create();
        $existingTimeslot = RequestTimeslot::factory()->create([
            'listing_id' => $request->listing_id,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHour(),
            'max_attendees' => 5,
            'is_active' => true,
        ]);

        // Create existing active appointment
        $existingAppointment = RequestAppointment::factory()->create([
            'listing_request_id' => $request->id,
            'timeslot_id' => $existingTimeslot->id,
            'status' => RequestAppointmentStatus::Pending,
        ]);

        // Create a different timeslot to try to book
        $newTimeslot = RequestTimeslot::factory()->create([
            'listing_id' => $request->listing_id,
            'starts_at' => now()->addDays(5),
            'ends_at' => now()->addDays(5)->addHour(),
            'max_attendees' => 5,
            'is_active' => true,
        ]);

        // Verify the existing appointment was created properly
        expect($request->appointments()->count())->toBe(1);

        // The component should prevent booking a second appointment
        $component = Livewire::test(\App\Livewire\ListingRequests\Requestee\Appointments::class, [
            'access_token' => $request->access_token,
        ]);

        $component->call('book', $newTimeslot);

        // Should still only have one appointment (the original)
        expect($request->fresh()->appointments()->count())->toBe(1);
    });

    it('can cancel an appointment', function () {
        $request = ListingRequest::factory()->confirmed()->create();
        $timeslot = RequestTimeslot::factory()->create([
            'listing_id' => $request->listing_id,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHour(),
        ]);
        $appointment = RequestAppointment::factory()->create([
            'listing_request_id' => $request->id,
            'timeslot_id' => $timeslot->id,
            'status' => RequestAppointmentStatus::Pending,
        ]);

        Livewire::test(\App\Livewire\ListingRequests\Requestee\Appointments::class, [
            'access_token' => $request->access_token,
        ])
            ->call('cancel', $appointment);

        expect($appointment->refresh()->status)->toBe(RequestAppointmentStatus::Cancelled);
    });
});

describe('timeslot management', function () {
    it('tracks remaining slots correctly', function () {
        $listing = Listing::factory()->published()->create();
        $timeslot = RequestTimeslot::factory()->create([
            'listing_id' => $listing->id,
            'max_attendees' => 3,
            'is_active' => true,
        ]);

        expect($timeslot->remainingSlots())->toBe(3);

        RequestAppointment::factory()->count(2)->create([
            'timeslot_id' => $timeslot->id,
            'status' => RequestAppointmentStatus::Pending,
        ]);

        expect($timeslot->remainingSlots())->toBe(1);
    });

    it('does not count cancelled appointments', function () {
        $timeslot = RequestTimeslot::factory()->create(['max_attendees' => 2]);

        RequestAppointment::factory()->create([
            'timeslot_id' => $timeslot->id,
            'status' => RequestAppointmentStatus::Cancelled,
        ]);

        expect($timeslot->remainingSlots())->toBe(2);
    });
});

describe('ical service', function () {
    it('generates valid ical content', function () {
        $request = ListingRequest::factory()->confirmed()->create();
        $timeslot = RequestTimeslot::factory()->create([
            'listing_id' => $request->listing_id,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHour(),
            'location' => 'Musterstraße 123, 12345 Berlin',
        ]);
        $appointment = RequestAppointment::factory()->create([
            'listing_request_id' => $request->id,
            'timeslot_id' => $timeslot->id,
        ]);

        $service = app(ICalService::class);
        $ical = $service->generateAppointmentIcal($appointment);

        expect($ical)->toContain('BEGIN:VCALENDAR');
        expect($ical)->toContain('BEGIN:VEVENT');
        expect($ical)->toContain('END:VEVENT');
        expect($ical)->toContain('END:VCALENDAR');
        expect($ical)->toContain('LOCATION:');
    });

    it('generates correct filename', function () {
        $timeslot = RequestTimeslot::factory()->create([
            'starts_at' => '2026-04-15 10:00:00',
        ]);
        $appointment = RequestAppointment::factory()->create([
            'timeslot_id' => $timeslot->id,
        ]);

        $service = app(ICalService::class);
        $filename = $service->getFilename($appointment);

        expect($filename)->toBe('besichtigung-2026-04-15.ics');
    });
});

describe('listing closure', function () {
    it('closes all active requests when listing is archived', function () {
        $listing = Listing::factory()->published()->create();
        $activeRequest = ListingRequest::factory()->confirmed()->create(['listing_id' => $listing->id]);
        $closedRequest = ListingRequest::factory()->closed()->create(['listing_id' => $listing->id]);

        app(ListingRequestService::class)->closeListing($listing, 'Wohnung wurde vermietet');

        expect($listing->refresh()->status->value)->toBe('archived');
        expect($activeRequest->refresh()->status)->toBe(ListingRequestStatus::Closed);
        expect($closedRequest->refresh()->status)->toBe(ListingRequestStatus::Closed);
    });
});
