<?php

namespace App\Policies;

use App\Models\RentalObject;
use App\Models\User;

class RentalObjectPolicy
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
    public function view(User $user, RentalObject $rentalObject): bool
    {
        return $this->isContact($user, $rentalObject);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RentalObject $rentalObject): bool
    {
        return $this->hasRole($user, $rentalObject, ['owner', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RentalObject $rentalObject): bool
    {
        return $this->hasRole($user, $rentalObject, ['owner']);
    }

    /**
     * Check if the user is a contact of the rental object.
     */
    private function isContact(User $user, RentalObject $rentalObject): bool
    {
        return $rentalObject->contacts()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if the user has one of the specified roles.
     *
     * @param  array<string>  $roles
     */
    private function hasRole(User $user, RentalObject $rentalObject, array $roles): bool
    {
        return $rentalObject->contacts()
            ->where('user_id', $user->id)
            ->whereIn('role', $roles)
            ->exists();
    }
}
