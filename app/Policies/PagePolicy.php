<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
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
        return $user->hasPageRole();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Page $page): bool
    {
        return $user->hasPageRole();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPageRole();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Page $page): bool
    {
        // Page admins can update any page
        if ($user->isPageAdmin()) {
            return true;
        }

        // Editors can only update pages they created
        return $page->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $page): bool
    {
        // Page admins can delete any page
        if ($user->isPageAdmin()) {
            return true;
        }

        // Editors can only delete pages they created
        return $page->created_by === $user->id;
    }

    /**
     * Determine whether the user can manage templates.
     */
    public function manageTemplates(User $user): bool
    {
        return $user->isPageAdmin();
    }

    /**
     * Determine whether the user can publish pages.
     */
    public function publish(User $user, Page $page): bool
    {
        // Page admins can publish any page
        if ($user->isPageAdmin()) {
            return true;
        }

        // Editors can only publish pages they created
        return $page->created_by === $user->id;
    }
}
