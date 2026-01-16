<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->isPublic()) {
            $now = now();

            $upcomingEvents = Event::where('start_date', '>=', $now)
                ->orderBy('start_date')
                ->with('creator')
                ->with(['registrations' => function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orderByDesc('created_at');
                }])
                ->get();

            $pastEvents = Event::where('end_date', '<', $now)
                ->orderByDesc('end_date')
                ->with('creator')
                ->get();

            return view('events.index', compact('upcomingEvents', 'pastEvents'));
        }

        $myEvents = $user
            ? Event::where('created_by', $user->id)
                ->orderByDesc('start_date')
                ->get()
            : collect();

        return view('events.index', compact('myEvents'));
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
        $user = Auth::user();
        if ($user && $user->isPublic()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'speakers' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'color' => 'nullable|string|max:7',
            'fee_amount' => 'nullable|numeric|min:0',
            'fee_currency' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255|required_with:fee_amount',
            'bank_account' => 'nullable|string|max:255|required_with:fee_amount',
        ]);

        $event = Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'speakers' => $validated['speakers'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'color' => $validated['color'] ?? '#007bff',
            'fee_amount' => $validated['fee_amount'] ?? null,
            'fee_currency' => $validated['fee_currency'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account' => $validated['bank_account'] ?? null,
            'created_by' => Auth::id(),
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'event_created',
            'description' => 'New event created: '.$event->title,
            'subject_type' => Event::class,
            'subject_id' => $event->id,
        ]);

        return response()->json(['success' => true, 'event' => $event]);
    }

    public function register(Request $request, Event $event)
    {
        $user = Auth::user();
        if (! $user || ! $user->isPublic()) {
            abort(403);
        }

        $hasFee = ! is_null($event->fee_amount);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'payment_proof' => $hasFee ? 'required|image|max:4096' : 'nullable|image|max:4096',
        ]);

        $path = null;
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('event_payments', 'public');
        }

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'payment_proof_path' => $path,
            'status' => $hasFee ? 'pending' : 'approved',
            'payment_verified_at' => $hasFee ? null : now(),
            'verified_by' => $hasFee ? null : $event->created_by,
        ]);

        return redirect()->route('events.index')
            ->with('success', 'You have registered for the event.');
    }

    public function attendees(Event $event)
    {
        $user = Auth::user();

        if (! $user || ! ($user->hasAdminRole() || $user->id === $event->created_by)) {
            abort(403);
        }

        $registrations = $event->registrations()
            ->with(['user', 'verifier'])
            ->orderByDesc('created_at')
            ->get();

        return view('events.attendees', compact('event', 'registrations'));
    }

    public function updateRegistration(Request $request, Event $event, EventRegistration $registration)
    {
        $user = Auth::user();

        if (! $user || ! ($user->hasAdminRole() || $user->id === $event->created_by)) {
            abort(403);
        }

        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $registration->update([
            'status' => $validated['status'],
            'payment_verified_at' => $validated['status'] === 'approved' ? now() : null,
            'verified_by' => $validated['status'] === 'approved' ? $user->id : null,
        ]);

        return redirect()->route('events.attendees', $event)
            ->with('success', 'Registration updated successfully.');
    }

    public function destroy(Event $event)
    {
        $user = Auth::user();
        if ($user && $user->isPublic()) {
            abort(403);
        }

        $event->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'event_deleted',
            'description' => 'Event deleted: '.$event->title,
        ]);

        return response()->json(['success' => true]);
    }
}
