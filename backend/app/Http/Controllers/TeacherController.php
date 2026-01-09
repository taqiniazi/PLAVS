<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Book;

class TeacherController extends Controller
{
    /**
     * Display list of students assigned to this teacher.
     */
    public function students()
    {
        $teacher = Auth::user();
        
        // Get students who have been assigned books by this teacher
        $students = User::where('role', 'student')
            ->whereHas('booksThroughAssignment', function ($query) use ($teacher) {
                $query->where('user_id', $teacher->id);
            })
            ->withCount('assignedBooks')
            ->get();
        
        return view('teachers.students', compact('students'));
    }
    
    /**
     * Display book assignments made by this teacher.
     */
    public function assignments()
    {
        $teacher = Auth::user();
        
        // Get books assigned by this teacher through the pivot table
        $assignedBooks = $teacher->booksThroughAssignment()
            ->with('category')
            ->get();
        
        return view('teachers.assignments', compact('assignedBooks'));
    }
}
