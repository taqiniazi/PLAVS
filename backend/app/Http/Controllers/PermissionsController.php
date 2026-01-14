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

        $ownerRequests = \App\Models\OwnerRequest::where('status', 'pending')
            ->with('user')
            ->get();

        $candidates = User::whereDoesntHave('ownerRequests', function ($q) {
                $q->where('status', 'pending');
            })
            ->get();

        return view('permissions.index', compact('candidates', 'ownerRequests'));
    }

    /**
     * Allow candidate users to request owner role
     */
    public function requestOwner(Request $request)
    {
        $user = Auth::user();
        if (!$user->isPublic()) {
            return redirect()->back()->with('error', 'Only public users can request owner role.');
        }

        $validated = $request->validate([
            'library_name' => 'required|string|max:255',
            'library_city' => 'nullable|string|max:120',
            'library_country' => 'nullable|string|max:120',
            'library_address' => 'nullable|string|max:255',
            'library_phone' => 'nullable|string|max:30',
        ]);

        // Avoid duplicate pending requests from same user
        $existing = \App\Models\OwnerRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
        if ($existing) {
            return redirect()->back()->with('error', 'You already have a pending owner request.');
        }

        \App\Models\OwnerRequest::create([
            'user_id' => $user->id,
            'library_name' => $validated['library_name'],
            'library_city' => $validated['library_city'] ?? null,
            'library_country' => $validated['library_country'] ?? null,
            'library_address' => $validated['library_address'] ?? null,
            'library_phone' => $validated['library_phone'] ?? null,
            'status' => 'pending',
        ]);

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
                User::ROLE_PUBLIC,
                User::ROLE_ADMIN,
            ]),
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Enforce business rule: if assigning OWNER, clear request flag
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('permissions.index')->with('success', 'Role updated for user: ' . $user->name);
    }

    /**
     * Approve an owner request and assign role to the requester
     */
    public function approveOwnerRequest(Request $request, \App\Models\OwnerRequest $ownerRequest)
    {
        $admin = Auth::user();
        if (!$admin->isSuperAdmin() && !$admin->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($ownerRequest->status !== 'pending') {
            return redirect()->route('permissions.index')->with('error', 'Request is not pending.');
        }

        $user = $ownerRequest->user;
        $user->role = User::ROLE_OWNER;
        $user->save();

        $ownerRequest->status = 'approved';
        $ownerRequest->approved_by = $admin->id;
        $ownerRequest->approved_at = now();
        $ownerRequest->save();

        return redirect()->route('permissions.index')->with('success', 'Owner request approved for user: ' . $user->name);
    }

    /**
     * Reject an owner request (admin/superadmin only)
     */
    public function rejectOwnerRequest(Request $request, \App\Models\OwnerRequest $ownerRequest)
    {
        $admin = Auth::user();
        if (!$admin->isSuperAdmin() && !$admin->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($ownerRequest->status !== 'pending') {
            return redirect()->route('permissions.index')->with('error', 'Request is not pending.');
        }

        $ownerRequest->status = 'rejected';
        $ownerRequest->approved_by = $admin->id;
        $ownerRequest->approved_at = now();
        $ownerRequest->notes = $request->input('notes');
        $ownerRequest->save();

        return redirect()->route('permissions.index')->with('success', 'Owner request rejected for user: ' . $ownerRequest->user->name);
    }
}
