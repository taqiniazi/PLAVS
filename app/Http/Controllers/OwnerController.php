<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = User::withCount('ownedBooks')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => ucfirst($user->role),
                'books_count' => $user->owned_books_count,
                'avatar' => $user->avatar ? asset('storage/'.$user->avatar) : asset('images/user.png'),
                'joined_date' => $user->created_at->format('M d, Y'),
            ];
        });

        return view('owners.index', compact('owners'));
    }

    public function show(User $user)
    {
        $user->loadCount('ownedBooks');
        // Load libraries owned by this user
        $libraries = \App\Models\Library::where('owner_id', $user->id)->withCount('books')->get();
        
        // Load latest owner request (for displaying submitted details even if library not created yet)
        $ownerRequest = $user->ownerRequests()->latest()->first();

        return view('owners.show', compact('user', 'libraries', 'ownerRequest'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $recipient = User::findOrFail($request->recipient_id);
        $sender = Auth::user();

        Mail::to($recipient->email)->send(new \App\Mail\OwnerMessage($sender, $request->subject, $request->message));

        return back()->with('success', 'Message sent successfully to ' . $recipient->name);
    }
}
