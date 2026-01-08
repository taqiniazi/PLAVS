<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();
        
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
        
        // Paginate results (15 per page) and append search query to pagination links
        $books = $query->paginate(15)->appends($request->query());
        
        return view('books.index', compact('books'));
    }

    public function create()
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

        return view('books.create', compact('authors', 'shelves', 'owners'));
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
            'owner' => 'required|string|max:255',
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
            'owner' => $validated['owner'],
            'description' => $validated['description'] ?? null,
            'visibility' => true,
            'status' => 'Available',
            'image' => 'book1.png'
        ];

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
                            \Log::warning('Scanned image URL returned non-image content type: ' . $contentType);
                        }
                    } else {
                        \Log::warning('Failed to download scanned image URL, status: ' . $response->status());
                    }
                } catch (\Exception $e) {
                    \Log::warning('Exception while downloading scanned image URL: ' . $e->getMessage());
                }
            } else {
                \Log::warning('Invalid scanned_image_url provided: ' . $scannedUrl);
            }
        }

        $book = Book::create($bookData);

        // Log activity
        $this->logActivity('book_added', 'New book added: ' . $book->title, $book);

        return redirect()->route('books.index')->with('success', 'Book added successfully!');
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
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
                    \Log::warning('Exception while downloading scanned image URL on update: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('books.manage')->with('success', 'Book updated successfully!');
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'owner' => 'required|string|max:255',
            'reason' => 'nullable|string|max:1000',
        ]);

        // Validate owner exists in users table
        $ownerExists = User::where('name', $validated['owner'])->exists();
        if (! $ownerExists) {
            return redirect()->route('books.manage')->withErrors(['owner' => 'Selected owner does not exist.']);
        }

        $book = Book::findOrFail($validated['book_id']);
        $oldOwner = $book->owner;
        $book->owner = $validated['owner'];
        $book->save();

        $this->logActivity('book_transferred', "Book '{$book->title}' transferred from {$oldOwner} to {$book->owner}. Reason: " . ($validated['reason'] ?? 'N/A'), $book);

        return redirect()->route('books.manage')->with('success', 'Book ownership transferred successfully!');
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
        $book->shelf_location = $validated['shelf_location'];
        $book->save();

        $this->logActivity('book_shelf_changed', "Book '{$book->title}' moved from {$old} to {$book->shelf_location}. Reason: " . ($validated['reason'] ?? 'N/A'), $book);

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

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.manage')->with('success', 'Book disposed successfully!');
    }

    public function manage(Request $request)
    {
        $query = Book::query();
        
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
        
        $books = $query->get();
        $users = User::all();
        return view('books.manage', compact('books', 'users'));
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
