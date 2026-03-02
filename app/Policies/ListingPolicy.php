<?php

namespace App\Policies;

use App\Models\Flat;
use App\Models\Listing;
use App\Models\User;

class ListingPolicy
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
    public function view(User $user, Listing $listing): bool
    {
        return $this->canManageListing($user, $listing);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Flat $flat): bool
    {
        return $this->hasRoleOnFlat($user, $flat, ['owner', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Listing $listing): bool
    {
        return $this->canManageListing($user, $listing);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Listing $listing): bool
    {
        return $this->canManageListing($user, $listing);
    }

    /**
     * Determine whether the user can manage timeslots for this listing.
     */
    public function manageTimeslots(User $user, Listing $listing): bool
    {
        return $this->canManageListing($user, $listing);
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
        if ($flat) {
            return $this->hasRoleOnFlat($user, $flat, ['owner', 'manager']);
        }

        return false;
    }

    /**
     * Check if the user has one of the specified roles on the flat's rental object.
     *
     * @param  array<string>  $roles
     */
    private function hasRoleOnFlat(User $user, Flat $flat, array $roles): bool
    {
        $rentalObject = $flat->rentalObject;
        if (! $rentalObject) {
            return false;
        }

        return $rentalObject->contacts()
            ->where('user_id', $user->id)
            ->whereIn('role', $roles)
            ->exists();
    }
}
