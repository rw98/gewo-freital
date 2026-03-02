<?php

namespace App\Services;

use App\Enums\ListingRequestStatus;
use App\Models\Listing;
use App\Models\ListingRequest;
use App\Models\User;
use App\Notifications\StatusChangedNotification;
use Illuminate\Support\Facades\Notification;

class ListingRequestService
{
    /**
     * Transition a listing request to a new status.
     *
     * @throws \InvalidArgumentException
     */
    public function transitionStatus(
        ListingRequest $request,
        ListingRequestStatus $newStatus,
        User $employee,
        ?string $rejectionReason = null
    ): ListingRequest {
        if (! $request->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition from {$request->status->value} to {$newStatus->value}"
            );
        }

        $data = ['status' => $newStatus];

        // Set timestamps based on new status
        match ($newStatus) {
            ListingRequestStatus::Approved => $data += [
                'approved_at' => now(),
                'approved_by' => $employee->id,
            ],
            ListingRequestStatus::Signed => $data += [
                'signed_at' => now(),
            ],
            ListingRequestStatus::Closed => $data += [
                'closed_at' => now(),
            ],
            ListingRequestStatus::Rejected => $data += [
                'rejected_at' => now(),
                'rejection_reason' => $rejectionReason,
            ],
            default => null,
        };

        $request->update($data);

        // Send notification to requestee
        Notification::route('mail', $request->email)
            ->notify(new StatusChangedNotification($request));

        return $request->refresh();
    }

    /**
     * Close a listing and notify all active requestees.
     */
    public function closeListing(Listing $listing, ?string $reason = null): void
    {
        // Update listing status to archived
        $listing->update([
            'status' => \App\Enums\ListingStatus::Archived,
        ]);

        // Get all active requests for this listing
        $activeRequests = $listing->requests()
            ->active()
            ->get();

        foreach ($activeRequests as $request) {
            // Close the request
            $request->update([
                'status' => ListingRequestStatus::Closed,
                'closed_at' => now(),
            ]);

            // Notify the requestee
            Notification::route('mail', $request->email)
                ->notify(new \App\Notifications\ListingClosedNotification($request, $reason));
        }
    }

    /**
     * Assign a request to an employee.
     */
    public function assignTo(ListingRequest $request, ?User $employee): ListingRequest
    {
        $request->update([
            'assigned_to' => $employee?->id,
        ]);

        return $request->refresh();
    }
}
