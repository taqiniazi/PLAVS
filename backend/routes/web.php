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

    Route::resource('books', BookController::class);

    // Library Routes
    Route::resource('libraries', LibraryController::class);
    Route::get('/libraries/{library}/invite', [LibraryController::class, 'generateInvite'])->name('libraries.generate_invite');
    Route::get('/join/{token}', [LibraryController::class, 'join'])->name('libraries.join');

    // Room Routes (nested under libraries)
    Route::resource('libraries.rooms', RoomController::class);

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
});
