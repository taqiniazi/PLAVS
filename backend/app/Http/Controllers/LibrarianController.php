<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Library;

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
        // Fetch owner's libraries to optionally show selection when more than one
        $libraries = Library::where('owner_id', $user->id)->get();
        return view('librarians.create', compact('libraries'));
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
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('users', 'username'),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['email'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_LIBRARIAN,
            'parent_owner_id' => $user->id,
        ]);

        return redirect()->route('libraries.index')
            ->with('success', 'Librarian account created successfully.');
    }

    /**
     * Remove a librarian (Owner-only, must belong to the owner).
     */
    public function destroy(User $librarian)
    {
        $user = Auth::user();
        if (!$user || !$user->isOwner()) {
            abort(403);
        }

        // Ensure target is a librarian linked to this owner
        if (!$librarian->isLibrarian() || $librarian->parent_owner_id !== $user->id) {
            abort(403);
        }

        $librarian->delete();

        return redirect()->route('libraries.index')
            ->with('success', 'Librarian removed successfully.');
    }
}