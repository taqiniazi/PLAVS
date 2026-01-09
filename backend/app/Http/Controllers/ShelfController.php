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
        
        if ($user->hasAdminRole()) {
            $shelves = Shelf::with(['room.library', 'books'])->get();
        } else {
            $shelves = Shelf::whereHas('room.library', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->with(['room.library', 'books'])->get();
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
        $user = Auth::user();
        
        if ($user->hasAdminRole()) {
            $rooms = Room::with('library')->get();
        } else {
            $rooms = Room::whereHas('library', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->with('library')->get();
        }

        return view('shelves.create', compact('rooms'));
    }

    /**
     * Store a newly created shelf.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'room_id' => 'required|exists:rooms,id',
        ]);

        // Generate shelf code if not provided
        $code = $request->code;
        if (empty($code)) {
            $room = Room::find($request->room_id);
            $shelfCount = Shelf::where('room_id', $request->room_id)->count() + 1;
            $code = $room->library->name[0] . '-' . $room->id . '-' . $shelfCount;
        }

        Shelf::create([
            'name' => $request->name,
            'code' => $code,
            'description' => $request->description,
            'room_id' => $request->room_id,
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
        
        if ($user->hasAdminRole()) {
            $rooms = Room::with('library')->get();
        } else {
            $rooms = Room::whereHas('library', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->with('library')->get();
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
            'room_id' => 'required|exists:rooms,id',
        ]);

        $shelf->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'room_id' => $request->room_id,
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
