<?php

namespace App\Http\Controllers;

use App\Models\Shelf;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShelfController extends Controller
{
    /**
     * Display a listing of the shelves.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $shelves = Shelf::with(['room.library', 'books'])->get();
        } elseif ($user->isOwner()) {
            $shelves = Shelf::whereHas('room.library', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->with(['room.library', 'books'])->get();
        } elseif ($user->isLibrarian()) {
            $shelves = Shelf::whereHas('room.library', function ($query) use ($user) {
                $query->where('owner_id', $user->parent_owner_id);
            })->with(['room.library', 'books'])->get();
        } else {
            $shelves = collect();
        }

        // Group shelves by library and room for better display
        $shelvesByLibrary = $shelves->groupBy(fn($shelf) => $shelf->room->library->name);

        return view('shelves.index', compact('shelves', 'shelvesByLibrary'));
    }

    /**
     * Show the form for creating a new shelf.
     */
    public function create()
    {
        $this->authorize('create', Shelf::class);
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $rooms = Room::with('library')->get();
            $libraries = \App\Models\Library::all();
        } elseif ($user->isOwner()) {
            $rooms = Room::whereHas('library', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->with('library')->get();
            $libraries = \App\Models\Library::where('owner_id', $user->id)->get();
        } elseif ($user->isLibrarian()) {
            $rooms = Room::whereHas('library', function ($query) use ($user) {
                $query->where('owner_id', $user->parent_owner_id);
            })->with('library')->get();
            $libraries = \App\Models\Library::where('owner_id', $user->parent_owner_id)->get();
        } else {
            $rooms = collect();
            $libraries = collect();
        }

        return view('shelves.create', compact('rooms', 'libraries'));
    }

    /**
     * Store a newly created shelf.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Shelf::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $user = Auth::user();
        if ($request->filled('room_id')) {
            $room = Room::with('library')->findOrFail($request->room_id);
            if (!($user->isAdmin() || ($user->isOwner() && $room->library && $room->library->owner_id === $user->id) || ($user->isLibrarian() && $room->library && $room->library->owner_id === $user->parent_owner_id))) {
                abort(403, 'You are not authorized to create a shelf in this room.');
            }
        }

        // Generate shelf code if not provided
        $code = $request->code;
        if (empty($code)) {
            if ($request->filled('room_id')) {
                $room = isset($room) ? $room : Room::find($request->room_id);
                $shelfCount = Shelf::where('room_id', $request->room_id)->count() + 1;
                $code = ($room && $room->library && isset($room->library->name[0]) ? $room->library->name[0] : 'S') . '-' . ($room ? $room->id : '0') . '-' . $shelfCount;
            } else {
                // Fallback generic code when no room is selected
                $code = 'S-' . time();
            }
        }

        Shelf::create([
            'name' => $request->name,
            'code' => $code,
            'description' => $request->description,
            'room_id' => $request->room_id, // can be null
            'library_id' => isset($room) && $room->library ? $room->library->id : null,
        ]);

        return redirect()->route('shelves.index')
            ->with('success', 'Shelf created successfully.');
    }

    /**
     * Display the specified shelf with its books.
     */
    public function show(Shelf $shelf)
    {
        $this->authorize('view', $shelf);
        
        $shelf->load(['room.library', 'books']);

        return view('shelves.show', compact('shelf'));
    }

    /**
     * Show the form for editing the shelf.
     */
    public function edit(Shelf $shelf)
    {
        $this->authorize('update', $shelf);
        
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $rooms = Room::with('library')->get();
        } elseif ($user->isOwner()) {
            $rooms = Room::whereHas('library', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->with('library')->get();
        } elseif ($user->isLibrarian()) {
            $rooms = Room::whereHas('library', function ($query) use ($user) {
                $query->where('owner_id', $user->parent_owner_id);
            })->with('library')->get();
        } else {
            $rooms = collect();
        }

        return view('shelves.edit', compact('shelf', 'rooms'));
    }

    /**
     * Update the specified shelf.
     */
    public function update(Request $request, Shelf $shelf)
    {
        $this->authorize('update', $shelf);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'room_id' => 'nullable|exists:rooms,id',
        ]);
        
        $libraryId = $shelf->library_id;
        if ($request->filled('room_id') && $request->room_id != $shelf->room_id) {
            $room = Room::with('library')->find($request->room_id);
            if ($room && $room->library) {
                $libraryId = $room->library->id;
            }
        }

        $shelf->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'room_id' => $request->room_id, // can be null
            'library_id' => $libraryId,
        ]);

        return redirect()->route('shelves.index')
            ->with('success', 'Shelf updated successfully.');
    }

    /**
     * Remove the specified shelf.
     */
    public function destroy(Shelf $shelf)
    {
        $this->authorize('delete', $shelf);

        $shelf->delete();

        return redirect()->route('shelves.index')
            ->with('success', 'Shelf deleted successfully.');
    }
}
