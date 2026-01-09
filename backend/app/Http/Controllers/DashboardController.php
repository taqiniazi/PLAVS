<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Library;
use App\Models\Shelf;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [];
        
        // Base stats for all users
        $stats['total_books'] = number_format(Book::count());
        $stats['active_members'] = number_format(User::count());
        $stats['books_borrowed'] = number_format(Book::where('status', 'like', 'Borrowed%')->count());
        $stats['book_shelves'] = number_format(Shelf::count());
        
        // Admin/Librarian/Owner specific stats
        if ($user->hasAdminRole()) {
            $stats['total_libraries'] = number_format(Library::count());
            $stats['total_rooms'] = \App\Models\Room::count();
            
            // Get libraries for overview
            $libraries = Library::with(['rooms', 'shelves', 'books'])->get();
            
        } else {
            $libraries = collect();
        }
        
        // Teacher specific stats
        if ($user->isTeacher()) {
            $stats['my_students'] = User::where('role', 'student')->count();
            $stats['books_assigned'] = Book::whereNotNull('assigned_user_id')->count();
            $stats['available_books'] = Book::where('status', 'Available')->count();
            $stats['my_libraries'] = Library::count();
            
            $my_students = User::where('role', 'student')->with('books')->get();
            $recently_assigned = Book::whereNotNull('assigned_user_id')
                ->with(['user', 'assignedUser'])
                ->latest()
                ->take(4)
                ->get();
                
        } else {
            $my_students = collect();
            $recently_assigned = collect();
        }
        
        // Student specific stats
        if ($user->isStudent()) {
            $stats['my_assigned_books'] = $user->assignedBooks()->count();
            $stats['books_read'] = $user->assignedBooks()->where('status', 'Returned')->count();
            $stats['currently_reading'] = $user->assignedBooks()->where('status', 'Borrowed')->count();
            $stats['my_teachers'] = User::where('role', 'teacher')->count();
            
            $my_assigned_books = $user->assignedBooks()->get();
        } else {
            $my_assigned_books = collect();
        }
        
        $recent_books = Book::latest()->take(4)->get()->map(function ($book) {
            return [
                'title' => strlen($book->title) > 18 ? substr($book->title, 0, 15) . '...' : $book->title,
                'author' => $book->author,
                'image' => $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/' . ($book->image ?? 'book1.png'))
            ];
        });

        $recent_activities = ActivityLog::with('user')
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($activity) {
                return [
                    'type' => ucfirst(str_replace('_', ' ', $activity->type)),
                    'description' => $activity->description,
                    'time' => $activity->created_at->diffForHumans(),
                    'user' => $activity->user->name ?? 'System'
                ];
            });

        return view('dashboard.index', compact(
            'stats', 
            'recent_books', 
            'recent_activities',
            'libraries',
            'my_students',
            'recently_assigned',
            'my_assigned_books'
        ));
    }
}
