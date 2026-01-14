<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Library;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $activeLibraryId = session('active_library_id');
        // Owners see only their libraries' rooms; admins see all; librarians see only parent owner's rooms
        if ($user->isAdmin()) {
            $roomsQuery = Room::with('library');
            if ($activeLibraryId) {
                $roomsQuery->where('library_id', $activeLibraryId);
            }
            $rooms = $roomsQuery->paginate(10);
        } elseif ($user->isOwner()) {
            $roomsQuery = Room::with('library')
                ->whereHas('library', function($q) use ($user) {
                    $q->where('owner_id', $user->id);
                });

            if ($activeLibraryId) {
                $roomsQuery->where('library_id', $activeLibraryId);
            }

            $rooms = $roomsQuery->paginate(10);
        } elseif ($user->isLibrarian()) {
            $roomsQuery = Room::with('library')
                ->whereHas('library', function($q) use ($user) {
                    $q->where('owner_id', $user->parent_owner_id);
                });

            if ($activeLibraryId) {
                $roomsQuery->where('library_id', $activeLibraryId);
            }

            $rooms = $roomsQuery->paginate(10);
        } else {
            // Others shouldn't see rooms management
            $rooms = Room::where('id', 0)->paginate(10);
        }

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Library $library)
    {
        $this->authorize('manageContent', $library);
        // Provide libraries list for owners/librarians with multiple libraries to allow switching
        $user = Auth::user();
        if ($user->isAdmin()) {
            $libraries = Library::all();
        } elseif ($user->isOwner()) {
            $libraries = Library::where('owner_id', $user->id)->get();
        } elseif ($user->isLibrarian()) {
            $libraries = Library::where('owner_id', $user->parent_owner_id)->get();
        } else {
            $libraries = collect();
        }
        return view('rooms.create', compact('library', 'libraries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Library $library)
    {
        $this->authorize('manageContent', $library);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $library->rooms()->create($validated);

        return redirect()->route('libraries.show', $library)
            ->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Library $library, Room $room)
    {
        $this->authorize('view', $room);
        $room->load('shelves');
        return view('rooms.show', compact('room', 'library'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        $this->authorize('update', $room);
        $user = Auth::user();

        if ($user->isAdmin()) {
            $libraries = Library::all();
        } elseif ($user->isOwner()) {
            $libraries = Library::where('owner_id', $user->id)->get();
        } elseif ($user->isLibrarian()) {
            $libraries = Library::where('owner_id', $user->parent_owner_id)->get();
        } else {
            $libraries = collect();
        }

        return view('rooms.edit', compact('room', 'libraries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $this->authorize('update', $room);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $libraryId = $room->library_id;

        if ($request->filled('library_id')) {
            $request->validate([
                'library_id' => 'integer|exists:libraries,id',
            ]);

            if ($user->isAdmin()) {
                $library = Library::where('id', $request->library_id)->firstOrFail();
            } elseif ($user->isOwner()) {
                $library = Library::where('id', $request->library_id)
                    ->where('owner_id', $user->id)
                    ->firstOrFail();
            } elseif ($user->isLibrarian()) {
                $library = Library::where('id', $request->library_id)
                    ->where('owner_id', $user->parent_owner_id)
                    ->firstOrFail();
            } else {
                abort(403);
            }

            $libraryId = $library->id;
        }

        $room->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'library_id' => $libraryId,
        ]);

        return redirect()->route('libraries.show', $room->library_id)
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $this->authorize('delete', $room);
        $libraryId = $room->library_id;
        $room->delete();

        return redirect()->route('libraries.show', $libraryId)
            ->with('success', 'Room deleted successfully.');
    }
}
