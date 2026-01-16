<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Library;
use App\Models\Shelf;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [];

        if ($user->hasAdminRole()) {
            if ($user->isAdmin()) {
                // Admin sees all
                $stats['total_books'] = number_format(Book::count());
                $stats['active_members'] = number_format(User::count());
                $stats['books_borrowed'] = number_format(Book::where('status', 'like', 'Borrowed%')->count());
                $stats['book_shelves'] = number_format(Shelf::count());
                $stats['total_libraries'] = number_format(Library::count());
                $stats['total_rooms'] = \App\Models\Room::count();

                $libraries = Library::withCount(['rooms', 'shelves', 'books'])->get();
            } elseif ($user->isOwner()) {
                // Owner sees only their own data
                $ownerId = $user->id;

                $stats['total_books'] = number_format(Book::where(function ($q) use ($ownerId, $user) {
                    $q->whereHas('shelf.room.library', function ($sq) use ($ownerId) {
                        $sq->where('owner_id', $ownerId);
                    })->orWhere('owner', $user->name);
                })->count());

                $stats['total_librarians'] = number_format(
                    User::where('role', User::ROLE_LIBRARIAN)
                        ->where('parent_owner_id', $user->id)
                        ->count()
                );

                $stats['book_shelves'] = number_format(Shelf::whereHas('room.library', function ($q) use ($ownerId) {
                    $q->where('owner_id', $ownerId);
                })->count());

                $stats['total_libraries'] = number_format(Library::where('owner_id', $ownerId)->count());
                $stats['total_rooms'] = number_format(\App\Models\Room::whereHas('library', function ($q) use ($ownerId) {
                    $q->where('owner_id', $ownerId);
                })->count());

                $libraries = Library::where('owner_id', $ownerId)->withCount(['rooms', 'shelves', 'books'])->get();
            } elseif ($user->isLibrarian()) {
                // Librarian sees parent owner's data
                $ownerId = $user->parent_owner_id;

                $stats['total_books'] = number_format(Book::where(function ($q) use ($ownerId, $user) {
                    $q->whereHas('shelf.room.library', function ($sq) use ($ownerId) {
                        $sq->where('owner_id', $ownerId);
                    })->orWhere('owner', optional($user->parentOwner)->name);
                })->count());

                $stats['active_members'] = number_format(User::whereHas('joinedLibraries', function ($q) use ($ownerId) {
                    $q->where('owner_id', $ownerId);
                })->count());

                $stats['book_shelves'] = number_format(Shelf::whereHas('room.library', function ($q) use ($ownerId) {
                    $q->where('owner_id', $ownerId);
                })->count());

                $stats['total_libraries'] = number_format(Library::where('owner_id', $ownerId)->count());
                $stats['total_rooms'] = number_format(\App\Models\Room::whereHas('library', function ($q) use ($ownerId) {
                    $q->where('owner_id', $ownerId);
                })->count());

                $libraries = Library::where('owner_id', $ownerId)->withCount(['rooms', 'shelves', 'books'])->get();
            }
        } else {
            $libraries = collect();
        }

        $my_students = collect();
        $recently_assigned = collect();

        // Public specific stats
        if ($user->isPublic()) {
            // Use ONLY active assignments for accurate count
            $stats['my_assigned_books'] = $user->assignedBooks()->count() + $user->activeAssignedBooks()->count();
            $stats['books_read'] = $user->assignedBooks()->where('status', 'Returned')->count()
                + $user->booksThroughAssignment()->where('status', 'Returned')->count();
            $stats['currently_reading'] = $user->assignedBooks()->where('status', 'Borrowed')->count()
                + $user->booksThroughAssignment()->where('status', 'Borrowed')->count();
            $stats['wishlist_count'] = $user->wishlist()->count();

            // Get both direct assigned books and ACTIVE pivot assigned books
            $directBooks = $user->assignedBooks()
                ->with('category')
                ->get()
                ->each(function ($book) {
                    // Use updated_at as a fallback assigned date for direct assignments
                    $book->assigned_date = $book->updated_at;
                });

            $pivotBooks = $user->activeAssignedBooks()
                ->with('category')
                ->get()
                ->each(function ($book) {
                    // Prefer pivot assigned_at if available, otherwise fallback to updated_at
                    $book->assigned_date = optional($book->pivot)->assigned_at ?? $book->updated_at;
                });

            // Merge, sort by assigned_date desc, remove duplicates, and take top 4
            $my_assigned_books = $directBooks
                ->merge($pivotBooks)
                ->sortByDesc('assigned_date')
                ->unique('id')
                ->take(4);
        } else {
            $my_assigned_books = collect();
        }

        $recent_books = Book::query()
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('books')
                    ->groupBy('title', 'author', 'shelf_id');
            })
            ->latest()
            ->take(4)
            ->get();

        $recent_activities = ActivityLog::with('user')
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($activity) {
                $desc = $activity->description;
                $desc = preg_replace_callback('/user ID (\d+)/i', function ($m) {
                    $u = \App\Models\User::find($m[1]);

                    return $u ? 'user '.$u->name : $m[0];
                }, $desc);

                return [
                    'type' => ucfirst(str_replace('_', ' ', $activity->type)),
                    'description' => $desc,
                    'time' => $activity->created_at->diffForHumans(),
                    'user' => $activity->user->name ?? 'System',
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
