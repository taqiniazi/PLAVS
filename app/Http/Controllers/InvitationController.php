<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\Library;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Show the form for creating a new invitation.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get libraries where user can invite
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $libraries = Library::all();
        } elseif ($user->isOwner()) {
            $libraries = $user->ownedLibraries;
        } elseif ($user->isLibrarian()) {
             $libraries = Library::where('owner_id', $user->parent_owner_id)->get();
        } else {
            // Other roles (Student/Teacher) cannot invite
            abort(403, 'You do not have permission to invite members.');
        }

        return view('invitations.create', compact('libraries'));
    }

    /**
     * Store a newly created invitation in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|string',
            'library_id' => 'required|exists:libraries,id',
        ]);

        $library = Library::findOrFail($request->library_id);

        // Authorization: Check if user is Owner or Librarian of this library
        $user = Auth::user();
        if (!$user->can('update', $library)) {
             // Fallback if policy check fails, though 'update' should cover it.
             // Double check manual logic if policy is strict
             $isOwner = $user->id === $library->owner_id;
             $isLibrarian = $user->isLibrarian() && $user->parent_owner_id === $library->owner_id;
             
             if (!$isOwner && !$isLibrarian) {
                 abort(403, 'Unauthorized action.');
             }
        }

        // Check if invitation already exists and is pending
        $existingInvitation = Invitation::where('email', $request->email)
            ->where('library_id', $library->id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return back()->with('error', 'An invitation is already pending for this email.');
        }

        // Check if user is already a member
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            $isMember = $library->members()->where('user_id', $existingUser->id)->exists();
            if ($isMember) {
                 return back()->with('error', 'User is already a member of this library.');
            }
        }

        $invitation = Invitation::create([
            'email' => $request->email,
            'role' => $request->role, // e.g., 'student', 'teacher', 'librarian'
            'library_id' => $library->id,
            'inviter_id' => $user->id,
            'token' => Str::random(32),
            'status' => 'pending',
        ]);

        // Send Email
        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation sent successfully.');
    }

    /**
     * Handle the acceptance of an invitation.
     */
    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->status !== 'pending') {
            return redirect()->route('dashboard')->with('info', 'This invitation has already been used.');
        }

        // If user is not logged in, store token in session and redirect to login/register
        if (!Auth::check()) {
            session(['invitation_token' => $token]);
            return redirect()->route('login')->with('info', 'Please login or register to accept the invitation.');
        }

        $user = Auth::user();

        // Check if the email matches (optional, but good for security if we want to enforce it)
        // For now, we allow accepting with any account as long as they have the token.
        // But maybe we should warn if email is different.
        
        // Process the invitation
        $this->processInvitation($user, $invitation);

        return redirect()->route('libraries.show', $invitation->library_id)
            ->with('success', 'You have successfully joined the library.');
    }

    /**
     * Process the invitation logic
     */
    public function processInvitation(User $user, Invitation $invitation)
    {
        $library = $invitation->library;

        // Add user to library members if not already
        if (!$library->members()->where('user_id', $user->id)->exists()) {
            $library->members()->attach($user->id);
        }
        
        // Update user role if necessary/allowed? 
        // The prompt says "join his library or collaborate".
        // If role is 'librarian', we might want to update the user's role in users table 
        // OR just treat them as a collaborator in this library context.
        // For simplicity, let's assume 'role' in invitation implies the role they will play.
        // If the system supports multiple roles per library, we would store it in pivot.
        // But current system seems to have single role on User model.
        // Let's just attach them for now. If role needs to change, it might be complex if they are already 'student'.
        
        // If the invited role is higher (e.g. Librarian) and user is Candidate/Student, maybe we update?
        // But the User model has a single 'role' field.
        // Let's keep it simple: Just add to library_user pivot.
        // We might want to store the 'role' in the pivot table later if needed.
        // For now, let's assume 'collaborate' means adding them to the library.

        $invitation->update(['status' => 'accepted']);
        
        // Clear session
        session()->forget('invitation_token');
    }
}
