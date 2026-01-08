<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_books' => number_format(Book::count()),
            'active_members' => number_format(User::count()),
            'books_borrowed' => number_format(Book::where('status', 'like', 'Borrowed%')->count()),
            'book_shelves' => number_format(Book::distinct('shelf_location')->count())
        ];

        $recent_books = Book::latest()->take(4)->get()->map(function ($book) {
            return [
                'title' => strlen($book->title) > 18 ? substr($book->title, 0, 15) . '...' : $book->title,
                'author' => $book->author,
                'image' => $book->image ?? 'book1.png'
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

        return view('dashboard.index', compact('stats', 'recent_books', 'recent_activities'));
    }
}
