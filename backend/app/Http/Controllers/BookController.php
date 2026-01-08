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
        ]);

        $bookData = [
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'edition' => $validated['edition'],
            'publisher' => $validated['publisher'],
            'publish_date' => $validated['publish_date'],
            'shelf_location' => $validated['shelf'],
            'owner' => $validated['owner'],
            'description' => $validated['description'],
            'visibility' => true,
            'status' => 'Available',
            'image' => 'book1.png' // Default image
        ];

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('books', 'public');
            $bookData['cover_image'] = $coverImagePath;
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
        ]);

        $book->update($validated);

        return redirect()->route('books.manage')->with('success', 'Book updated successfully!');
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
        return view('books.manage', compact('books'));
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
