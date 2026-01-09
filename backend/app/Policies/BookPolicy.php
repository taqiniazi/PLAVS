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
        // Admins, Librarians, Owners can view all books
        if ($user->hasAdminRole()) {
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
        // Admins, Librarians, Owners can view any book
        if ($user->hasAdminRole()) {
            return true;
        }

        // Check if book is assigned to this user
        return $book->assignedUsers()->where('user_id', $user->id)->exists() ||
               $book->assigned_user_id === $user->id;
    }

    /**
     * Determine whether the user can create books.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminRole();
    }

    /**
     * Determine whether the user can update the book.
     */
    public function update(User $user, Book $book): bool
    {
        return $user->hasAdminRole();
    }

    /**
     * Determine whether the user can delete the book.
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->hasAdminRole();
    }

    /**
     * Determine whether the user can restore the book.
     */
    public function restore(User $user, Book $book): bool
    {
        return $user->hasAdminRole();
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
        return $user->canAssignBooks() && $user->hasAdminRole() || $user->isTeacher();
    }
}
