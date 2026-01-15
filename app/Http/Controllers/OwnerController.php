<?php

namespace App\Http\Controllers;

use App\Models\User;

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
}
