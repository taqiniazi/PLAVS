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
        
        // Get books assigned to this student
        $assignedBooks = $user->assignedBooks()->with('category')->get();
        
        return view('student.assigned-books', compact('assignedBooks'));
    }
}
