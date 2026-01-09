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
        
        // Get books assigned to this student through pivot table with return info
        $assignedBooks = $user->booksThroughAssignment()
            ->with('category')
            ->orderByPivot('assigned_at', 'desc')
            ->get();
        
        return view('student.assigned-books', compact('assignedBooks'));
    }

    /**
     * Return a book assigned to the student.
     */
    public function returnBook(Request $request, Book $book)
    {
        $user = Auth::user();
        
        // Find the pivot record
        $pivot = $user->booksThroughAssignment()->where('book_id', $book->id)->first();
        
        if (!$pivot) {
            return redirect()->route('student.assigned-books')
                ->with('error', 'Book not found in your assigned books.');
        }
        
        if ($pivot->pivot->is_returned) {
            return redirect()->route('student.assigned-books')
                ->with('error', 'This book has already been returned.');
        }
        
        // Update the pivot record
        $user->booksThroughAssignment()->updateExistingPivot($book->id, [
            'is_returned' => true,
            'return_date' => now(),
            'return_notes' => $request->input('return_notes', '')
        ]);
        
        // Update book status
        $book->status = 'Available';
        $book->assigned_user_id = null;
        $book->save();
        
        return redirect()->route('student.assigned-books')
            ->with('success', 'Book returned successfully!');
    }
}
