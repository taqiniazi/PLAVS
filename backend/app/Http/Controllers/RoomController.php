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
        $rooms = Room::with('library')->paginate(10);
        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $libraries = Library::all();
        return view('rooms.create', compact('libraries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'library_id' => 'required|exists:libraries,id',
            'description' => 'nullable|string',
        ]);

        Room::create($validated);

        return redirect()->route('libraries.show', $request->library_id)
            ->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Library $library, Room $room)
    {
        $room->load('shelves');
        return view('rooms.show', compact('room', 'library'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        $libraries = Library::all();
        return view('rooms.edit', compact('room', 'libraries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'library_id' => 'required|exists:libraries,id',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.show', [$request->library_id, $room])
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $libraryId = $room->library_id;
        $room->delete();

        return redirect()->route('libraries.show', $libraryId)
            ->with('success', 'Room deleted successfully.');
    }
}
