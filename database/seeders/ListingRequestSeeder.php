<?php

namespace Database\Seeders;

use App\Enums\ListingRequestStatus;
use App\Models\Listing;
use App\Models\ListingRequest;
use App\Models\RequestAppointment;
use App\Models\RequestDocument;
use App\Models\RequestMessage;
use App\Models\RequestTimeslot;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listings = Listing::published()->get();
        $employees = User::all();

        if ($listings->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('No published listings or users found. Skipping ListingRequestSeeder.');

            return;
        }

        foreach ($listings as $listing) {
            // Create timeslots for each listing
            $employee = $employees->random();
            $timeslots = RequestTimeslot::factory()
                ->count(3)
                ->for($listing)
                ->create(['created_by' => $employee->id]);

            // Create a mix of requests in different states
            $this->createRequestInStatus($listing, $employees, $timeslots, ListingRequestStatus::Confirmed);
            $this->createRequestInStatus($listing, $employees, $timeslots, ListingRequestStatus::AppointmentPending, true);
            $this->createRequestInStatus($listing, $employees, $timeslots, ListingRequestStatus::WaitingForApproval);
            $this->createRequestInStatus($listing, $employees, $timeslots, ListingRequestStatus::Rejected);
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, User>  $employees
     * @param  \Illuminate\Database\Eloquent\Collection<int, RequestTimeslot>  $timeslots
     */
    private function createRequestInStatus(
        Listing $listing,
        $employees,
        $timeslots,
        ListingRequestStatus $status,
        bool $withAppointment = false
    ): void {
        $employee = $employees->random();

        $request = ListingRequest::factory()
            ->for($listing)
            ->create([
                'status' => $status,
                'assigned_to' => $employee->id,
                'email_confirmed_at' => now()->subDays(rand(1, 7)),
                'rejected_at' => $status === ListingRequestStatus::Rejected ? now() : null,
                'rejection_reason' => $status === ListingRequestStatus::Rejected ? 'Bonität nicht ausreichend' : null,
            ]);

        // Add some documents
        RequestDocument::factory()
            ->count(rand(1, 3))
            ->for($request, 'listingRequest')
            ->create();

        // Add some messages
        RequestMessage::factory()
            ->count(rand(1, 4))
            ->for($request, 'listingRequest')
            ->create();

        RequestMessage::factory()
            ->count(rand(1, 2))
            ->for($request, 'listingRequest')
            ->fromEmployee($employee)
            ->create();

        // Add appointment if needed
        if ($withAppointment && $timeslots->isNotEmpty()) {
            RequestAppointment::factory()
                ->for($request, 'listingRequest')
                ->for($timeslots->random(), 'timeslot')
                ->confirmed()
                ->create();
        }
    }
}
