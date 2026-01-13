<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any books.
     */
    public function viewAny(User $user): bool
    {
        // Admins, Librarians, Owners can view books (controller applies scoping)
        if ($user->hasAdminRole() || $user->isLibrarian() || $user->isOwner()) {
            return true;
        }

        // Teachers and Students can only view their assigned books
        return $user->isTeacher() || $user->isStudent();
    }

    /**
     * Determine whether the user can view the book.
     */
    public function view(User $user, Book $book): bool
    {
        // Admins can view any book
        if ($user->hasAdminRole()) {
            return true;
        }

        // Owners can view their books
        if ($user->isOwner()) {
            return ($book->shelf && $book->shelf->room && $book->shelf->room->library && $book->shelf->room->library->owner_id === $user->id)
                || $book->owner === $user->name
                || $book->assignedUsers()->where('user_id', $user->id)->exists()
                || $book->assigned_user_id === $user->id;
        }

        // Librarians can view only parent owner's books
        if ($user->isLibrarian()) {
            return ($book->shelf && $book->shelf->room && $book->shelf->room->library && $book->shelf->room->library->owner_id === $user->parent_owner_id)
                || $book->owner === optional($user->parentOwner)->name
                || $book->assignedUsers()->where('user_id', $user->id)->exists()
                || $book->assigned_user_id === $user->id;
        }

        // Teachers/Students: only assigned
        return $book->assignedUsers()->where('user_id', $user->id)->exists() ||
               $book->assigned_user_id === $user->id;
    }

    /**
     * Determine whether the user can create books.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminRole() || $user->isLibrarian() || $user->isOwner();
    }

    /**
     * Determine whether the user can update the book.
     */
    public function update(User $user, Book $book): bool
    {
        // Admins unrestricted
        if ($user->hasAdminRole()) return true;

        // Owners: only their books
        if ($user->isOwner()) {
            return ($book->shelf && $book->shelf->room && $book->shelf->room->library && $book->shelf->room->library->owner_id === $user->id)
                || $book->owner === $user->name;
        }
        
        // Librarians: only parent owner's books
        if ($user->isLibrarian()) {
            return ($book->shelf && $book->shelf->room && $book->shelf->room->library && $book->shelf->room->library->owner_id === $user->parent_owner_id)
                || $book->owner === optional($user->parentOwner)->name;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the book.
     */
    public function delete(User $user, Book $book): bool
    {
        return $this->update($user, $book);
    }

    /**
     * Determine whether the user can restore the book.
     */
    public function restore(User $user, Book $book): bool
    {
        return $this->update($user, $book);
    }

    /**
     * Determine whether the user can permanently delete the book.
     */
    public function forceDelete(User $user, Book $book): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can assign the book.
     */
    public function assign(User $user, Book $book): bool
    {
        return $this->update($user, $book);
    }
}
