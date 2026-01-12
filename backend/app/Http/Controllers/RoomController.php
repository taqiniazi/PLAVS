<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Library;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        // Owners see only their libraries' rooms; admins/librarians see all
        if ($user->isOwner() && !$user->hasAdminRole()) {
            $rooms = Room::with('library')
                ->whereHas('library', function($q) use ($user) {
                    $q->where('owner_id', $user->id);
                })
                ->paginate(10);
        } else {
            $rooms = Room::with('library')->paginate(10);
        }

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Library $library)
    {
        $this->authorize('manageContent', $library);
        return view('rooms.create', compact('library'));
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
        return view('rooms.edit', compact('room'));
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

        $room->update($validated);

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
