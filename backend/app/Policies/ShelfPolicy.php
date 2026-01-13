<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Shelf;
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
               ($user->isOwner() && $shelf->room->library->owner_id === $user->id);
    }

    /**
     * Determine whether the user can create shelves.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminRole() || $user->isOwner();
    }

    /**
     * Determine whether the user can update the shelf.
     */
    public function update(User $user, Shelf $shelf): bool
    {
        return $user->hasAdminRole() || 
               ($user->isOwner() && $shelf->room->library->owner_id === $user->id);
    }

    /**
     * Determine whether the user can delete the shelf.
     */
    public function delete(User $user, Shelf $shelf): bool
    {
        return $user->hasAdminRole() || 
               ($user->isOwner() && $shelf->room->library->owner_id === $user->id);
    }
}
