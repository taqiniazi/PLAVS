<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LibrarianController extends Controller
{
    /**
     * Show the form for creating a new librarian (Owner-only).
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->isOwner()) {
            abort(403);
        }
        return view('librarians.create');
    }

    /**
     * Store a newly created librarian linked to the authenticated Owner.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isOwner()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_LIBRARIAN,
            'parent_owner_id' => $user->id,
        ]);

        return redirect()->route('libraries.index')
            ->with('success', 'Librarian account created successfully.');
    }
}