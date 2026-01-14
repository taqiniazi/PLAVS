<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\Room;
use App\Models\Shelf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LibraryController extends Controller
{
    /**
     * Display a listing of the libraries.
     */
    public function index()
    {
        $user = Auth::user();

        // Base query with eager loading and count
        $query = Library::with('owner')->withCount('books');

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            $libraries = $query->get();
        } elseif ($user->isLibrarian()) {
            $libraries = $query->where('owner_id', $user->parent_owner_id)->get();
        } elseif ($user->isOwner()) {
            // Show owned libraries AND joined libraries
            $libraries = $query->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('members', function($subQ) use ($user) {
                      $subQ->where('user_id', $user->id);
                  });
            })->get();
        } else {
            $libraries = $query->where('type', 'public')->get();
        }

        // Provide owner-linked librarians list for management UI
        $librarians = collect();
        if ($user->isOwner()) {
            $librarians = User::where('role', User::ROLE_LIBRARIAN)
                ->where('parent_owner_id', $user->id)
                ->get();
        }

        return view('libraries.index', compact('libraries', 'librarians'));
    }

    /**
     * Show the form for creating a new library.
     */
    public function create()
    {
        $this->authorize('create', Library::class);
        return view('libraries.create');
    }

    /**
     * Store a newly created library.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Library::class);
        $user = Auth::user();

        if ($user->isOwner()) {
            // Logged-in owner: skip owner email/password validation and use current user
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:public,private',
                'location' => 'nullable|string',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
            ]);

            DB::transaction(function () use ($request, $user) {
                // Create Library owned by the logged-in owner
                $library = Library::create([
                    'name' => $request->name,
                    'type' => $request->type,
                    'location' => $request->location,
                    'description' => $request->description,
                    'owner_id' => $user->id,
                    'contact_email' => $request->contact_email,
                    'contact_phone' => $request->contact_phone,
                ]);

                // Handle image upload
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('library_images', 'public');
                    $library->image = $imagePath;
                    $library->save();
                }
            });

            return redirect()->route('libraries.index')
                ->with('success', 'Library created successfully!');
        }

        // Admin/Superadmin creating a new owner + library (supports existing owner by email)
        // Base validation for library and owner email
        $baseRules = [
            // Library validation
            'name' => 'required|string|max:255',
            'type' => 'required|in:public,private',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Contact validation
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            // Owner email is always required
            'owner_email' => 'required|email',
        ];

        // Normalize email to avoid case/whitespace mismatches when checking existing owner
        $ownerEmailNormalized = Str::lower(trim($request->owner_email));
        $existingOwner = User::where(function ($q) use ($ownerEmailNormalized) {
            $q->where('email', $ownerEmailNormalized)
              ->orWhere('username', $ownerEmailNormalized);
        })->first();

        if ($existingOwner) {
            $request->validate($baseRules);
        } else {
            $request->validate($baseRules + [
                'owner_name' => 'required|string|max:255',
                'owner_phone' => 'required|string|max:20',
                'owner_password' => 'required|string|min:8|confirmed',
                // enforce uniqueness when creating a new owner
                'owner_email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email'),
                    Rule::unique('users', 'username'),
                ],
            ]);
        }

        DB::transaction(function () use ($request, $existingOwner, $ownerEmailNormalized) {
            // 1. Resolve Owner user
            $user = $existingOwner ?: User::create([
                'name' => $request->owner_name,
                'username' => $ownerEmailNormalized,
                'email' => $ownerEmailNormalized,
                'phone' => $request->owner_phone,
                'password' => Hash::make($request->owner_password),
                'role' => User::ROLE_OWNER ?? 'owner',
            ]);

            // 2. Create Library
            $library = Library::create([
                'name' => $request->name,
                'type' => $request->type,
                'location' => $request->location,
                'description' => $request->description,
                'owner_id' => $user->id,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
            ]);

            // 3. Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('library_images', 'public');
                $library->image = $imagePath;
                $library->save();
            }
        });

        return redirect()->route('libraries.index')
            ->with('success', 'Library created successfully!');
    }

    /**
     * Display the specified library.
     */
    public function show(Library $library)
    {
        $this->authorize('view', $library);
        
        $library->load(['rooms.shelves.books', 'books']);

        return view('libraries.show', compact('library'));
    }

    /**
     * Show the form for editing the library.
     */
    public function edit(Library $library)
    {
        $this->authorize('update', $library);
        
        return view('libraries.edit', compact('library'));
    }

    /**
     * Update the specified library.
     */
    public function update(Request $request, Library $library)
    {
        $this->authorize('update', $library);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:public,private',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Update library information
        $library->update([
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        return redirect()->route('libraries.index')
            ->with('success', 'Library updated successfully.');
    }

    /**
     * Remove the specified library.
     */
    public function destroy(Library $library)
    {
        $this->authorize('delete', $library);

        $library->delete();

        return redirect()->route('libraries.index')
            ->with('success', 'Library deleted successfully.');
    }

    /**
     * Generate invite token for private library.
     */
    public function generateInvite(Library $library)
    {
        $this->authorize('update', $library);

        $library->invite_token = Str::random(32);
        $library->save();

        return redirect()->back()
            ->with('success', 'Invite token generated.')
            ->with('invite_url', route('libraries.join', $library->invite_token));
    }

    /**
     * Join a library via invite token.
     */
    public function join(string $token)
    {
        $library = Library::where('invite_token', $token)->firstOrFail();

        if (!$library->isPrivate()) {
            return redirect()->route('dashboard')
                ->with('error', 'This invite link is invalid.');
        }

        // Add user to library (you might want to create a library_users pivot table)
        // For now, we'll just redirect with success
        return view('libraries.join', compact('library'));
    }

    /**
     * Display a listing of other libraries (not owned by current user).
     */
    public function otherLibraries()
    {
        $user = Auth::user();
        if (!$user || !($user->isOwner() || $user->isLibrarian())) {
            abort(403);
        }

        $query = Library::with('owner')->withCount('books');

        if ($user->isOwner()) {
            $libraries = $query->where('owner_id', '!=', $user->id)->get();
        } elseif ($user->isLibrarian()) {
            $libraries = $query->where('owner_id', '!=', $user->parent_owner_id)->get();
        } else {
            $libraries = collect();
        }

        return view('libraries.other_index', compact('libraries'));
    }

    /**
     * Display books of a specific other library.
     */
    public function otherLibraryBooks(Library $library)
    {
        $user = Auth::user();
        if (!$user || !($user->isOwner() || $user->isLibrarian())) {
            abort(403);
        }
        
        // Ensure we are not viewing our own library via this route
        $ownerId = $user->isOwner() ? $user->id : $user->parent_owner_id;
        if ($library->owner_id == $ownerId) {
             return redirect()->route('libraries.show', $library);
        }

        $books = \App\Models\Book::whereHas('shelf.room', function($q) use ($library) {
            $q->where('library_id', $library->id);
        })->get();

        return view('libraries.other_books', compact('library', 'books'));
    }

    /**
     * Switch active library for the logged-in owner or librarian.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'library_id' => 'required|integer',
        ]);

        $user = Auth::user();
        if (! ($user && ($user->isOwner() || $user->isLibrarian()))) {
            abort(403);
        }

        $ownerId = $user->isOwner() ? $user->id : $user->parent_owner_id;

        $library = Library::where('id', $request->library_id)
            ->where('owner_id', $ownerId)
            ->firstOrFail();

        session(['active_library_id' => $library->id]);

        return redirect()->back()->with('success', 'Switched to library: ' . $library->name);
    }
}
