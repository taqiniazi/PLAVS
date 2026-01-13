<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;

class StudentController extends Controller
{
    /**
     * Display a listing of the student's assigned books.
     */
    public function assignedBooks()
    {
        $user = Auth::user();
        
        // Get direct assigned books (via assigned_user_id)
        $directBooks = $user->assignedBooks()
            ->with('category')
            ->get()
            ->each(function ($book) {
                // Fallback assigned date for direct assignments
                $book->assigned_date = $book->updated_at;
            });
        
        // Get active (non-returned) books assigned through pivot table
        $pivotBooks = $user->activeAssignedBooks()
            ->with('category')
            ->get()
            ->each(function ($book) {
                // Prefer pivot assigned_at if available
                $book->assigned_date = optional($book->pivot)->assigned_at ?? $book->updated_at;
            });
        
        // Merge both sources, sort by assigned_date desc, and de-duplicate by book id
        $assignedBooks = $directBooks
            ->merge($pivotBooks)
            ->sortByDesc('assigned_date')
            ->unique('id');
        
        return view('student.assigned-books', compact('assignedBooks'));
    }

    /**
     * Return a book assigned to the student.
     */
    public function returnBook(Request $request, Book $book)
    {
        $user = Auth::user();
        
        // Snapshot assigned_at before modifying the book (fallback to updated_at)
        $assignedAtSnapshot = $book->updated_at;
        
        // Find the pivot record for this student's assignment
        $pivotRecord = $user->booksThroughAssignment()->where('book_id', $book->id)->first();
        
        if ($pivotRecord && !$pivotRecord->pivot->is_returned) {
            // Update existing pivot to mark as returned
            $user->booksThroughAssignment()->updateExistingPivot($book->id, [
                'is_returned' => true,
                'return_date' => now(),
                'return_notes' => $request->input('return_notes', '')
            ]);
        } elseif (!$pivotRecord && $book->assigned_user_id === $user->id) {
            // Directly assigned book (no pivot yet): create historical pivot and mark as returned
            $user->booksThroughAssignment()->attach($book->id, [
                'assignment_type' => 'admin_assign',
                'notes' => $request->input('return_notes', '') ?: null,
                'assigned_at' => $assignedAtSnapshot ?? now(),
                'return_date' => now(),
                'is_returned' => true,
                'return_notes' => $request->input('return_notes', '') ?: null,
            ]);
        } else {
            return redirect()->route('student.assigned-books')
                ->with('error', 'Book not found in your assigned books or already returned.');
        }
        
        // Update book status to Available and clear direct assignment
        $book->status = 'Available';
        $book->assigned_user_id = null;
        $book->save();
        
        return redirect()->route('student.assigned-books')
            ->with('success', 'Book returned successfully!');
    }
}
