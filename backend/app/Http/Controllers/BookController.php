<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Shelf;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Determine base query based on role
        if ($user && ($user->canViewAllBooks() || $user->isLibrarian())) {
            // Admins, Owners, Librarians start with all books (then filtered)
            $query = Book::query();
        } else {
            // Students/Teachers: only show their assigned books
            $query = $user ? $user->booksThroughAssignment() : Book::query();
            
            // For students, only show currently assigned (non-returned) books
            if ($user && $user->isStudent()) {
                 $query->wherePivot('is_returned', false);
            }
        }

        // Owner Scope: Show only books in their libraries or owned by them
        if ($user && $user->isOwner()) {
            $query->where(function($q) use ($user) {
                // Books in shelves belonging to libraries owned by this user
                $q->whereHas('shelf.room.library', function($subQ) use ($user) {
                    $subQ->where('owner_id', $user->id);
                })
                // OR books explicitly owned by this user (e.g. unshelved)
                ->orWhere('owner', $user->name);
            });

            // Active library filter (if selected in session)
            $activeLibraryId = session('active_library_id');
            if ($activeLibraryId) {
                $query->whereHas('shelf.room.library', function($q) use ($activeLibraryId) {
                    $q->where('id', $activeLibraryId);
                });
            }
        }

        // Librarian Scope: Show only books in parent owner's libraries or owned by parent
        if ($user && $user->isLibrarian()) {
            $query->where(function($q) use ($user) {
                $q->whereHas('shelf.room.library', function($libQ) use ($user) {
                    $libQ->where('owner_id', $user->parent_owner_id);
                })
                ->orWhere('owner', optional($user->parentOwner)->name);
            });
        }
        
        // Check if search parameter exists
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('author', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('isbn', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('publisher', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Pagination
        if ($user && ($user->canViewAllBooks() || $user->isLibrarian())) {
            $books = $query->paginate(15)->appends($request->query());
        } else {
            // For assigned books (students/teachers), usually get all or paginate
            $books = $query->orderByPivot('assigned_at', 'desc')->paginate(15)->appends($request->query());
        }
        
        return view('books.index', compact('books'));
    }

    public function create()
    {
        $user = Auth::user();
        $activeLibraryId = session('active_library_id');
        $ownerId = $user->isOwner() ? $user->id : ($user->isLibrarian() ? $user->parent_owner_id : null);
        $shelves = Shelf::whereHas('room.library', function ($q) use ($ownerId, $activeLibraryId) {
            if ($ownerId) {
                $q->where('owner_id', $ownerId);
            }
            if ($activeLibraryId) {
                $q->where('id', $activeLibraryId);
            }
        })->orderBy('name')->get();

        return view('books.create', compact('shelves'));
    }

    public function store(Request $request)
    {
        // Basic validation first
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'edition' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'publish_date' => 'nullable|date',
            'shelf' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'scanned_image_url' => 'nullable|string'
        ]);

        // Ensure either a file upload or a scanned URL is present
        $scannedUrl = trim($request->input('scanned_image_url', ''));
        if (! $request->hasFile('cover_image') && empty($scannedUrl)) {
            return redirect()->back()->withErrors(['cover_image' => 'Please upload a cover image or use the scanner.'])->withInput();
        }

        $bookData = [
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'] ?? null,
            'edition' => $validated['edition'] ?? null,
            'publisher' => $validated['publisher'] ?? null,
            'publish_date' => $validated['publish_date'] ?? null,
            'shelf_location' => $validated['shelf'],
            'owner' => Auth::user()->name,
            'description' => $validated['description'] ?? null,
            'visibility' => true,
            'status' => 'Available'
        ];

        // Map shelf name to shelf_id if it exists
        $shelfModel = Shelf::where('name', $validated['shelf'])->first();
        if ($shelfModel) {
            $bookData['shelf_id'] = $shelfModel->id;
        }

        // Scenario A: user uploaded a file
        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/books', $filename, 'public');
            $bookData['cover_image'] = $path;

        // Scenario B: downloaded from scanned_image_url
        } elseif (!empty($scannedUrl)) {
            // Validate URL
            if (filter_var($scannedUrl, FILTER_VALIDATE_URL)) {
                try {
                    $response = \Illuminate\Support\Facades\Http::get($scannedUrl);
                    if ($response->successful()) {
                        $contentType = $response->header('Content-Type', 'image/jpeg');
                        if (strpos($contentType, 'image/') === 0) {
                            $mime = substr($contentType, 6);
                            // normalize common types
                            if ($mime === 'jpeg' || $mime === 'pjpeg') $ext = 'jpg';
                            elseif ($mime === 'png') $ext = 'png';
                            elseif ($mime === 'gif') $ext = 'gif';
                            else $ext = 'jpg';

                            $filename = 'google_book_' . time() . '_' . uniqid() . '.' . $ext;
                            $path = 'uploads/books/' . $filename;
                            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $response->body());
                            $bookData['cover_image'] = $path;
                        } else {
                            Log::warning('Scanned image URL returned non-image content type: ' . $contentType);
                        }
                    } else {
                        Log::warning('Failed to download scanned image URL, status: ' . $response->status());
                    }
                } catch (\Exception $e) {
                    Log::warning('Exception while downloading scanned image URL: ' . $e->getMessage());
                }
            } else {
                Log::warning('Invalid scanned_image_url provided: ' . $scannedUrl);
            }
        }

        $book = Book::create($bookData);

        // Log activity
        $this->logActivity('book_added', 'New book added: ' . $book->title, $book);

        return redirect()->route('books.index')->with('success', 'Book added successfully!');
    }

    public function show(Book $book)
    {
        $ratings = $book->ratings()->with('user')->orderByDesc('created_at')->get();
        $userRating = auth()->check() ? $book->ratings()->where('user_id', auth()->id())->first() : null;
        return view('books.show', compact('book', 'ratings', 'userRating'));
    }

    public function edit(Book $book)
    {
        $authors = [
            'Eric Verzuh',
            'Scott Berkun',
            'Robert C. Martin',
            'Martin Fowler',
            'Kent Beck',
            'Uncle Bob'
        ];

        $shelves = [
            'Shelf A-1',
            'Shelf A-2',
            'Shelf B-1',
            'Shelf B-2',
            'Shelf C-1',
            'Shelf C-2',
            'Shelf C-3',
            'Shelf C-4'
        ];

        $owners = [
            'Taqi Raza Khan',
            'Library Admin',
            'Sarah Ahmed',
            'Ali Khan'
        ];

        return view('books.edit', compact('book', 'authors', 'shelves', 'owners'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'edition' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'publish_date' => 'nullable|date',
            'shelf_location' => 'required|string|max:50',
            'owner' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'boolean',
            'status' => 'required|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'scanned_image_url' => 'nullable|string'
        ]);

        // Update core fields
        $book->update($validated);

        // Handle new cover upload or scanned image URL
        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/books', $filename, 'public');
            $book->cover_image = $path;
            $book->save();
        } elseif ($request->filled('scanned_image_url')) {
            $scannedUrl = $request->input('scanned_image_url');
            if (filter_var($scannedUrl, FILTER_VALIDATE_URL)) {
                try {
                    $response = \Illuminate\Support\Facades\Http::get($scannedUrl);
                    if ($response->successful()) {
                        $contentType = $response->header('Content-Type', 'image/jpeg');
                        if (strpos($contentType, 'image/') === 0) {
                            $mime = substr($contentType, 6);
                            if ($mime === 'jpeg' || $mime === 'pjpeg') $ext = 'jpg';
                            elseif ($mime === 'png') $ext = 'png';
                            elseif ($mime === 'gif') $ext = 'gif';
                            else $ext = 'jpg';

                            $filename = 'google_book_' . time() . '_' . uniqid() . '.' . $ext;
                            $path = 'uploads/books/' . $filename;
                            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $response->body());
                            $book->cover_image = $path;
                            $book->save();
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Exception while downloading scanned image URL on update: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('books.manage')->with('success', 'Book updated successfully!');
    }


    public function changeShelf(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'shelf_location' => 'required|string|max:50',
            'reason' => 'nullable|string|max:1000',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $old = $book->shelf_location;
        
        // Find the shelf by name and update shelf_id
        $shelf = \App\Models\Shelf::where('name', $validated['shelf_location'])->first();
        
        if ($shelf) {
            $book->shelf_id = $shelf->id;
            $book->shelf_location = $validated['shelf_location'];
        } else {
            // If shelf not found, just update the shelf_location field
            $book->shelf_location = $validated['shelf_location'];
        }
        
        $book->save();

        $this->logActivity('book_shelf_changed', "Book '{$book->title}' moved from {$old} to {$book->shelf_location}. Reason: " . ($validated['reason'] ?? 'N/A'), $book);

        // Handle AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Book shelf changed successfully!',
                'new_shelf_name' => $book->shelf_location,
                'book_id' => $book->id
            ]);
        }

        return redirect()->route('books.manage')->with('success', 'Book shelf changed successfully!');
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'assigned_user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $book->assigned_user_id = $validated['assigned_user_id'];
        $book->status = 'Assigned';
        $book->save();

        $this->logActivity('book_assigned', "Book '{$book->title}' assigned to user ID {$validated['assigned_user_id']}. Reason: " . ($validated['reason'] ?? 'N/A'), $book);

        return redirect()->route('books.manage')->with('success', 'Book assigned successfully!');
    }

    public function returnBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'condition' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);
    
        $book = Book::findOrFail($validated['book_id']);
        $confirmer = Auth::user();

        // Guard against mis-assigned pivot records: if a student is confirming a return, force the pivot to attach/update to the confirmer
        if ($confirmer && method_exists($confirmer, 'isStudent') && $confirmer->isStudent()) {
            $validated['user_id'] = $confirmer->id;
        }
        // Ensure the pivot user matches the book's direct assignment holder when present
        if ($book->assigned_user_id && $validated['user_id'] !== $book->assigned_user_id) {
            $validated['user_id'] = $book->assigned_user_id;
        }

        // Snapshot the last known assignment/update time before we modify the book
        $assignedAtSnapshot = $book->updated_at;
        
        // Update previous holder pivot record to reflect return
        $previousHolder = User::find($validated['user_id']);
        if ($previousHolder) {
            $pivotRelation = $previousHolder->booksThroughAssignment();
            // Only update pivot if it already exists for this (user, book)
            $hasPivot = $pivotRelation->wherePivot('book_id', $book->id)->exists();
            if ($hasPivot) {
                $pivotRelation->updateExistingPivot($book->id, [
                    'is_returned' => true,
                    'return_date' => now(),
                    'return_notes' => $validated['notes'] ?? null,
                ]);
            } else {
                // Create a historical pivot record for direct assignments so Return History updates
                $pivotRelation->attach($book->id, [
                    'assignment_type' => 'admin_assign',
                    'notes' => $validated['notes'] ?? null,
                    'assigned_at' => $assignedAtSnapshot ?? now(),
                    'return_date' => now(),
                    'is_returned' => true,
                    'return_notes' => $validated['notes'] ?? null,
                ]);
            }
        }
        
        // Role-based status after return: Owner/Admin -> In Stock (Available)
        if ($confirmer->isOwner() || $confirmer->hasAdminRole()) {
            $book->status = 'Available';
            $book->assigned_user_id = null;
        } else {
            // Fallback for non-admins: keep assigned to confirmer
            $book->status = 'Assigned';
            $book->assigned_user_id = $confirmer->id;
        }
        $book->save();

        $this->logActivity(
            'book_return_confirmed',
            "Book '{$book->title}' return confirmed by user ID " . $confirmer->id . ". Previous holder user ID {$validated['user_id']}. Condition: " . ($validated['condition'] ?? 'N/A') . ". Notes: " . ($validated['notes'] ?? 'N/A'),
            $book
        );

        // Handle AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Book return confirmed. Book is now In Stock.',
                'book_id' => $book->id
            ]);
        }

        return redirect()->route('books.manage')->with('success', 'Book return confirmed. Book is now In Stock.');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.manage')->with('success', 'Book disposed successfully!');
    }

    public function manage(Request $request)
    {
        $user = Auth::user();
        $query = Book::query();

        // Owner scope restriction: Owners can only see books from their own libraries
        if ($user->isOwner()) { 
            $query->where(function($q) use ($user) {
                // Books shelved under libraries owned by this user
                $q->whereHas('shelf.room.library', function($q2) use ($user) {
                    $q2->where('owner_id', $user->id);
                })
                // Include unshelved books explicitly owned by this user
                ->orWhere(function($q2) use ($user) {
                    $q2->whereNull('shelf_id')
                       ->where('owner', $user->name);
                });
            });
        }

        // Librarian scope restriction: Only parent owner's libraries
        if ($user->isLibrarian()) {
            $query->where(function($q) use ($user) {
                $q->whereHas('shelf.room.library', function($q2) use ($user) {
                    $q2->where('owner_id', $user->parent_owner_id);
                })
                ->orWhere(function($q2) use ($user) {
                    $q2->whereNull('shelf_id')
                       ->where('owner', optional($user->parentOwner)->name);
                });
            });
        }

        // Check if search parameter exists
        if ($request->has('search') && !empty($request->search)) {
        
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('author', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('isbn', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('publisher', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('owner', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $books = $query->with(['shelf.room.library'])->get();
        $users = User::all();
        $shelves = \App\Models\Shelf::all();
        return view('books.manage', compact('books', 'users', 'shelves'));
    }

    /**
     * Show transferred/lent books for the current owner
     */
    public function transferredBooks(Request $request)
    {
        $user = Auth::user();
        
        // Only owners can view transferred books
        if (!$user->isOwner()) {
            abort(403, 'Unauthorized access');
        }

        $query = Book::whereHas('shelf.room.library', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where(function($q) {
            $q->where('status', 'transferred')
              ->orWhere('status', 'assigned')
              ->orWhereNotNull('assigned_user_id');
        });

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('author', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('assignedUser', function($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        $transferredBooks = $query->with(['assignedUser', 'shelf.room.library'])->get();
        
        return view('books.transferred', compact('transferredBooks'));
    }

    /**
     * Transfer book to another library or user
     */

    /**
     * Transfer book to another library or user (Owner-specific)
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'transfer_to' => 'required|in:library,user', // specific target type
            'target_id' => 'required', // ID of the library or user
        ]);

        $book = Book::findOrFail($request->book_id);

        // Authorization: Only Owner/Admin can transfer
        // if (!auth()->user()->isOwnerOf($book)) { abort(403); }

        if ($request->transfer_to === 'library') {
            // Transfer to another Library
            // 1. You might want to set a 'transferred_to_library_id' column
            // 2. Or change the shelf/room to the new library's default.
            // For now, let's assume we mark it as transferred:
            $book->update([
                'status' => 'transferred',
                // 'current_library_id' => $request->target_id
                // Add specific logic here based on your Schema
            ]);
            $msg = "Book transferred to Library successfully.";

        } else {
            // Transfer/Assign to a User (Teacher/Student)
            // Check if user exists
            $user = \App\Models\User::findOrFail($request->target_id);
            
            // Attach to pivot table
            $user->assignedBooks()->attach($book->id, [
                'assigned_at' => now(),
                'status' => 'active',
                // 'assigned_by' => auth()->id()
            ]);

            $book->update(['status' => 'borrowed']); // or 'assigned'
            $msg = "Book assigned to User successfully.";
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function toggleVisibility(Request $request, Book $book)
    {
        $validated = $request->validate([
            'visibility' => 'required|boolean',
        ]);

        $book->visibility = $validated['visibility'];
        $book->save();

        $this->logActivity('book_visibility_changed', "Book '{$book->title}' visibility changed to " . ($book->visibility ? 'Public' : 'Private'), $book);

        return response()->json([
            'success' => true,
            'message' => 'Book visibility updated successfully!',
            'book_id' => $book->id,
            'new_visibility' => $book->visibility
        ]);
    }

    public function details(Book $book)
    {
        return response()->json($book);
    }

    private function logActivity($type, $description, $subject = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => $type,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
        ]);
    }
}
