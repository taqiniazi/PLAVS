<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\Room;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        if ($user->isSuperAdmin() || $user->isAdmin() || $user->isLibrarian()) {
            $libraries = $query->get();
        } elseif ($user->isOwner()) {
            $libraries = $query->where('owner_id', $user->id)->get();
        } else {
            // Students and Teachers can only see public libraries
            $libraries = $query->where('type', 'public')->get();
        }

        return view('libraries.index', compact('libraries'));
    }

    /**
     * Show the form for creating a new library.
     */
    public function create()
    {
        return view('libraries.create');
    }

    /**
     * Store a newly created library.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:public,private',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $library = Library::create([
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            'description' => $request->description,
            'owner_id' => Auth::id(),
        ]);

        return redirect()->route('libraries.index')
            ->with('success', 'Library created successfully.');
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
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'owner_phone' => 'required|string|max:20',
        ]);

        // Update library information
        $library->update([
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        // Update owner information if owner exists
        if ($library->owner) {
            $library->owner->update([
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'phone' => $request->owner_phone,
            ]);
        }

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
}
