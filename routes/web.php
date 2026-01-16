<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ShelfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Invitation Routes (Public)
Route::get('/invitations/accept/{token}', [App\Http\Controllers\InvitationController::class, 'accept'])->name('invitations.accept');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'showPasswordForm'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Book Routes - Custom routes MUST come before resource routes
    Route::get('/books/manage', [BookController::class, 'manage'])->name('books.manage');
    Route::get('/books/{book}/details', [BookController::class, 'details'])->name('books.details');
    Route::get('/books/{book}/assigned-users', [BookController::class, 'assignedUsers'])->name('books.assigned-users');

    // Book action routes
    Route::post('/books/transfer', [BookController::class, 'transfer'])->name('books.transfer');
    Route::post('/books/change-shelf', [BookController::class, 'changeShelf'])->name('books.change_shelf');
    Route::post('/books/assign', [BookController::class, 'assign'])->name('books.assign');
    Route::post('/books/return', [BookController::class, 'returnBook'])->name('books.return');
    Route::post('/books/recall', [BookController::class, 'recallBook'])->name('books.recall');
    Route::post('/books/{book}/toggle-visibility', [BookController::class, 'toggleVisibility'])->name('books.toggle_visibility');

    Route::resource('books', BookController::class);

    // Library Routes
    Route::get('/libraries/other', [LibraryController::class, 'otherLibraries'])->name('libraries.other');
    Route::get('/libraries/other/{library}/books', [LibraryController::class, 'otherLibraryBooks'])->name('libraries.other.books');
    Route::resource('libraries', LibraryController::class);
    Route::post('/libraries/switch', [LibraryController::class, 'switch'])->name('libraries.switch');
    Route::get('/libraries/{library}/invite', [LibraryController::class, 'generateInvite'])->name('libraries.generate_invite');
    Route::get('/join/{token}', [LibraryController::class, 'join'])->name('libraries.join');

    // Invitation Routes
    Route::get('/invitations/create', [App\Http\Controllers\InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [App\Http\Controllers\InvitationController::class, 'store'])->name('invitations.store');

    // Wishlist Routes
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{book}', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Rating Routes
    Route::post('/books/{book}/rating', [App\Http\Controllers\RatingController::class, 'store'])->name('books.rating.store');
    Route::get('/api/books/{book}/user-rating', [App\Http\Controllers\RatingController::class, 'getUserRating'])->name('api.books.user-rating');

    // Wishlist API Routes
    Route::get('/api/wishlist/check/{book}', [App\Http\Controllers\WishlistController::class, 'check'])->name('api.wishlist.check');

    // Room Routes (nested under libraries)
    Route::get('/libraries/{library}/rooms/create', [App\Http\Controllers\RoomController::class, 'create'])->name('libraries.rooms.create');
    Route::post('/libraries/{library}/rooms', [App\Http\Controllers\RoomController::class, 'store'])->name('libraries.rooms.store');

    // Rooms Index
    Route::get('/rooms', [App\Http\Controllers\RoomController::class, 'index'])->name('rooms.index');

    // Route to view a specific room (Fixes the crash)
    Route::get('/libraries/{library}/rooms/{room}', [App\Http\Controllers\RoomController::class, 'show'])->name('libraries.rooms.show');

    // Route for Editing/Deleting Rooms (if not using full resource)
    Route::get('/rooms/{room}/edit', [App\Http\Controllers\RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [App\Http\Controllers\RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [App\Http\Controllers\RoomController::class, 'destroy'])->name('rooms.destroy');

    // Shelf Routes
    Route::resource('shelves', ShelfController::class);

    // Event Routes
    Route::resource('events', EventController::class)->only(['index', 'store', 'destroy']);
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::put('/events/{event}/registrations/{registration}', [EventController::class, 'updateRegistration'])->name('events.registrations.update');

    // Owner Routes
    Route::get('/owners', [OwnerController::class, 'index'])->name('owners.index');

    // Public Routes
    Route::get('/public/assigned-books', [PublicController::class, 'assignedBooks'])->name('public.assigned-books');
    Route::post('/public/return-book/{book}', [PublicController::class, 'returnBook'])->name('public.return-book');

    // Notifications Routes
    Route::post('/notifications/clear', function () {
        session(['notifications_cleared_at' => now()]);

        return back();
    })->name('notifications.clear');

    // Permissions Routes (Admin/Superadmin only view; candidates can request)
    Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/assign-role', [PermissionsController::class, 'assignRole'])->name('permissions.assign-role');
    Route::post('/permissions/request-owner', [PermissionsController::class, 'requestOwner'])->name('permissions.request-owner');
    Route::post('/permissions/owner-requests/{ownerRequest}/approve', [PermissionsController::class, 'approveOwnerRequest'])->name('permissions.owner-requests.approve');
    Route::post('/permissions/owner-requests/{ownerRequest}/reject', [PermissionsController::class, 'rejectOwnerRequest'])->name('permissions.owner-requests.reject');
});

// API Routes
Route::middleware('auth')->group(function () {
    Route::get('/api/events', [EventController::class, 'api'])->name('api.events');

    // API for fetching rooms by library (for dynamic shelf creation)
    Route::get('/api/libraries/{library}/rooms', function (\App\Models\Library $library) {
        $rooms = $library->rooms()->get(['id', 'name']);

        return response()->json(['rooms' => $rooms]);
    })->name('api.libraries.rooms');
});

// Librarian management (Owner-only)
Route::middleware(['auth'])->group(function () {
    Route::get('/librarians/create', [LibrarianController::class, 'create'])->name('librarians.create');
    Route::post('/librarians', [LibrarianController::class, 'store'])->name('librarians.store');
    Route::delete('/librarians/{librarian}', [LibrarianController::class, 'destroy'])->name('librarians.destroy');
});
