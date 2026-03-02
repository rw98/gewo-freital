<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\RequestTimeslot;
use App\Models\User;

class RequestTimeslotPolicy
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
    public function view(User $user, RequestTimeslot $requestTimeslot): bool
    {
        return $this->canManageListing($user, $requestTimeslot->listing);
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
    public function update(User $user, RequestTimeslot $requestTimeslot): bool
    {
        return $this->canManageListing($user, $requestTimeslot->listing);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RequestTimeslot $requestTimeslot): bool
    {
        return $this->canManageListing($user, $requestTimeslot->listing);
    }

    /**
     * Check if the user can manage the listing.
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
