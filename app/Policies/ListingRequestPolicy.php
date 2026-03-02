<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\ListingRequest;
use App\Models\User;

class ListingRequestPolicy
{
    /**
     * Perform pre-authorization checks.
     * Admin users can perform any action.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ListingRequest $listingRequest): bool
    {
        return $this->canManageListing($user, $listingRequest->listing);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Listing $listing): bool
    {
        return $this->canManageListing($user, $listing);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ListingRequest $listingRequest): bool
    {
        return $this->canManageListing($user, $listingRequest->listing);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ListingRequest $listingRequest): bool
    {
        return $this->canManageListing($user, $listingRequest->listing);
    }

    /**
     * Determine whether the user can transition the status.
     */
    public function transitionStatus(User $user, ListingRequest $listingRequest): bool
    {
        return $this->canManageListing($user, $listingRequest->listing);
    }

    /**
     * Determine whether the user can manage timeslots for the listing.
     */
    public function manageTimeslots(User $user, Listing $listing): bool
    {
        return $this->canManageListing($user, $listing);
    }

    /**
     * Determine whether the user can send messages on the request.
     */
    public function sendMessage(User $user, ListingRequest $listingRequest): bool
    {
        return $this->canManageListing($user, $listingRequest->listing);
    }

    /**
     * Determine whether the user can upload documents to the request.
     */
    public function uploadDocument(User $user, ListingRequest $listingRequest): bool
    {
        return $this->canManageListing($user, $listingRequest->listing);
    }

    /**
     * Check if the user can manage the listing (is the creator or assigned contact).
     */
    private function canManageListing(User $user, Listing $listing): bool
    {
        // Creator can always manage
        if ($listing->created_by === $user->id) {
            return true;
        }

        // Check if the user is a contact of the rental object
        $flat = $listing->flat;
        if ($flat && $flat->rentalObject) {
            return $flat->rentalObject->contacts()
                ->where('user_id', $user->id)
                ->whereIn('role', ['owner', 'manager'])
                ->exists();
        }

        return false;
    }
}
