<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    /**
     * Display a listing of the public user's assigned books.
     */
    public function assignedBooks()
    {
        $user = Auth::user();

        $directBooks = $user->assignedBooks()
            ->with('category')
            ->get()
            ->each(function ($book) {
                $book->assigned_date = $book->updated_at;
            });

        $pivotBooks = $user->activeAssignedBooks()
            ->with('category')
            ->get()
            ->each(function ($book) {
                $book->assigned_date = optional($book->pivot)->assigned_at ?? $book->updated_at;
            });

        $assignedBooks = $directBooks
            ->merge($pivotBooks)
            ->sortByDesc('assigned_date')
            ->unique('id');

        return view('public.assigned-books', compact('assignedBooks'));
    }

    /**
     * Return a book assigned to the public user.
     */
    public function returnBook(Request $request, Book $book)
    {
        $user = Auth::user();

        $assignedAtSnapshot = $book->updated_at;

        $pivotRecord = $user->booksThroughAssignment()->where('book_id', $book->id)->first();

        if ($pivotRecord && ! $pivotRecord->pivot->is_returned) {
            $user->booksThroughAssignment()->updateExistingPivot($book->id, [
                'is_returned' => true,
                'return_date' => now(),
                'return_notes' => $request->input('return_notes', ''),
            ]);
        } elseif (! $pivotRecord && $book->assigned_user_id === $user->id) {
            $user->booksThroughAssignment()->attach($book->id, [
                'assignment_type' => 'admin_assign',
                'notes' => $request->input('return_notes', '') ?: null,
                'assigned_at' => $assignedAtSnapshot ?? now(),
                'return_date' => now(),
                'is_returned' => true,
                'return_notes' => $request->input('return_notes', '') ?: null,
            ]);
        } else {
            return redirect()->route('public.assigned-books')
                ->with('error', 'Book not found in your assigned books or already returned.');
        }

        $book->status = 'Available';
        $book->assigned_user_id = null;
        $book->save();

        return redirect()->route('public.assigned-books')
            ->with('success', 'Book returned successfully!');
    }
}
