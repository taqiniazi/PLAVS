<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

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

    // Book action routes
    Route::post('/books/transfer', [BookController::class, 'transfer'])->name('books.transfer');
    Route::post('/books/change-shelf', [BookController::class, 'changeShelf'])->name('books.change_shelf');
    Route::post('/books/assign', [BookController::class, 'assign'])->name('books.assign');
    Route::post('/books/return', [BookController::class, 'returnBook'])->name('books.return');
    Route::post('/books/recall', [BookController::class, 'recallBook'])->name('books.recall');
    Route::post('/books/{book}/toggle-visibility', [BookController::class, 'toggleVisibility'])->name('books.toggle_visibility');

    Route::resource('books', BookController::class);

    // Library Routes
    Route::resource('libraries', LibraryController::class);
    Route::get('/libraries/{library}/invite', [LibraryController::class, 'generateInvite'])->name('libraries.generate_invite');
    Route::get('/join/{token}', [LibraryController::class, 'join'])->name('libraries.join');

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
    
    // Owner Routes
    Route::get('/owners', [OwnerController::class, 'index'])->name('owners.index');

    // Teacher Routes
    Route::get('/teachers/students', [TeacherController::class, 'students'])->name('teachers.students');
    Route::get('/teachers/assignments', [TeacherController::class, 'assignments'])->name('teachers.assignments');

    // Student Routes
    Route::get('/student/assigned-books', [StudentController::class, 'assignedBooks'])->name('student.assigned-books');
    Route::post('/student/return-book/{book}', [StudentController::class, 'returnBook'])->name('student.return-book');
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
