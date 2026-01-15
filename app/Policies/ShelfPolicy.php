<?php

namespace App\Policies;

use App\Models\Shelf;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShelfPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any shelves.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminRole();
    }

    /**
     * Determine whether the user can view the shelf.
     */
    public function view(User $user, Shelf $shelf): bool
    {
        return $user->hasAdminRole() ||
               ($user->isOwner() && $shelf->room->library->owner_id === $user->id) ||
               ($user->isLibrarian() && $shelf->room->library->owner_id === $user->parent_owner_id);
    }

    /**
     * Determine whether the user can create shelves.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminRole() || $user->isOwner() || $user->isLibrarian();
    }

    /**
     * Determine whether the user can update the shelf.
     */
    public function update(User $user, Shelf $shelf): bool
    {
        return $user->hasAdminRole() ||
               ($user->isOwner() && $shelf->room->library->owner_id === $user->id) ||
               ($user->isLibrarian() && $shelf->room->library->owner_id === $user->parent_owner_id);
    }

    /**
     * Determine whether the user can delete the shelf.
     */
    public function delete(User $user, Shelf $shelf): bool
    {
        return $user->hasAdminRole() ||
               ($user->isOwner() && $shelf->room->library->owner_id === $user->id) ||
               ($user->isLibrarian() && $shelf->room->library->owner_id === $user->parent_owner_id);
    }
}
