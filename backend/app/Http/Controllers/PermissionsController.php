<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PermissionsController extends Controller
{
    /**
     * Display permissions dashboard (admin/superadmin only)
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $candidates = User::where('role', User::ROLE_CANDIDATE)->get();
        $ownerRequests = User::where('requested_owner', true)->get();

        return view('permissions.index', compact('candidates', 'ownerRequests'));
    }

    /**
     * Allow candidate users to request owner role
     */
    public function requestOwner(Request $request)
    {
        $user = Auth::user();
        if (!$user->isCandidate()) {
            return redirect()->back()->with('error', 'Only candidates can request owner role.');
        }

        $user->requested_owner = true;
        $user->save();

        return redirect()->back()->with('success', 'Owner role request submitted successfully.');
    }

    /**
     * Assign role to a user (admin/superadmin only)
     */
    public function assignRole(Request $request)
    {
        $admin = Auth::user();
        if (!$admin->isSuperAdmin() && !$admin->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:' . implode(',', [
                User::ROLE_OWNER,
                User::ROLE_LIBRARIAN,
                User::ROLE_TEACHER,
                User::ROLE_STUDENT,
                User::ROLE_ADMIN,
            ]),
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Enforce business rule: if assigning OWNER, clear request flag
        $user->role = $validated['role'];
        if ($validated['role'] === User::ROLE_OWNER) {
            $user->requested_owner = false;
        }
        $user->save();

        return redirect()->route('permissions.index')->with('success', 'Role updated for user: ' . $user->name);
    }
}