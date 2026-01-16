<?php

namespace App\Policies;

use App\Models\Library;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibraryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any libraries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminRole();
    }

    /**
     * Determine whether the user can view the library.
     */
    public function view(User $user, Library $library): bool
    {
        // Admins, Super Admins can view all libraries
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }

        // Owner can view their own library
        if ($user->isOwner() && $library->owner_id === $user->id) {
            return true;
        }

        // Librarians of the owner can view
        if ($user->isLibrarian() && $user->parent_owner_id === $library->owner_id) {
            return true;
        }

        // Members of the library can view
        if ($library->members()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Public libraries are visible to all authenticated users
        if ($library->isPublic()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create libraries.
     */
    public function create(User $user): bool
    {
        return $user->isOwner() || $user->isAdmin() || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the library.
     */
    public function update(User $user, Library $library): bool
    {
        return $user->isSuperAdmin() ||
               $user->isAdmin() ||
               ($user->isOwner() && $library->owner_id === $user->id);
    }

    /**
     * Determine whether the user can delete the library.
     */
    public function delete(User $user, Library $library): bool
    {
        return $user->isSuperAdmin() ||
               $user->isAdmin() ||
               ($user->isOwner() && $library->owner_id === $user->id);
    }

    /**
     * Determine whether the user can manage library content.
     */
    public function manageContent(User $user, Library $library): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }

        if ($user->isLibrarian() && $library->owner_id === $user->parent_owner_id) {
            return true;
        }

        if ($user->isOwner() && $library->owner_id === $user->id) {
            return true;
        }

        if ($user->isOwner() && $library->members()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }
}
