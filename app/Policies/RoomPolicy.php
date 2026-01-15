<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any rooms.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminRole();
    }

    /**
     * Determine whether the user can view the room.
     */
    public function view(User $user, Room $room): bool
    {
        return $user->hasAdminRole() ||
               ($user->isOwner() && $room->library->owner_id === $user->id) ||
               ($user->isLibrarian() && $room->library->owner_id === $user->parent_owner_id);
    }

    /**
     * Determine whether the user can create rooms.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminRole() || $user->isOwner() || $user->isLibrarian();
    }

    /**
     * Determine whether the user can update the room.
     */
    public function update(User $user, Room $room): bool
    {
        return $user->hasAdminRole() ||
               ($user->isOwner() && $room->library->owner_id === $user->id) ||
               ($user->isLibrarian() && $room->library->owner_id === $user->parent_owner_id);
    }

    /**
     * Determine whether the user can delete the room.
     */
    public function delete(User $user, Room $room): bool
    {
        return $user->hasAdminRole() ||
               ($user->isOwner() && $room->library->owner_id === $user->id) ||
               ($user->isLibrarian() && $room->library->owner_id === $user->parent_owner_id);
    }
}
