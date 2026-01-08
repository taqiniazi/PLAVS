<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        return view('events.index');
    }

    public function api()
    {
        $events = Event::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date->toISOString(),
                'end' => $event->end_date->toISOString(),
                'color' => $event->color,
                'description' => $event->description,
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'color' => 'nullable|string|max:7',
        ]);

        $event = Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'color' => $validated['color'] ?? '#007bff',
            'created_by' => Auth::id(),
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'event_created',
            'description' => 'New event created: ' . $event->title,
            'subject_type' => Event::class,
            'subject_id' => $event->id,
        ]);

        return response()->json(['success' => true, 'event' => $event]);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'event_deleted',
            'description' => 'Event deleted: ' . $event->title,
        ]);

        return response()->json(['success' => true]);
    }
}
