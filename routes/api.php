<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Routes
Route::post('/auth/register', [AuthController::class, 'apiRegister']);
Route::post('/auth/login', [AuthController::class, 'apiLogin']);

Route::get('/libraries', [LibraryController::class, 'apiIndex']);
Route::get('/libraries/{library}', [LibraryController::class, 'apiShow']);
Route::get('/libraries/{library}/books', [LibraryController::class, 'apiBooks']);

Route::get('/books', [BookController::class, 'apiIndex']);
Route::get('/books/{book}', [BookController::class, 'apiShow']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'apiLogout']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'apiShow']);
    Route::put('/profile', [ProfileController::class, 'apiUpdate']);
    Route::put('/profile/password', [ProfileController::class, 'apiUpdatePassword']);

    // Books Management
    Route::post('/books', [BookController::class, 'apiStore']);
    Route::put('/books/{book}', [BookController::class, 'apiUpdate']);
    Route::delete('/books/{book}', [BookController::class, 'apiDestroy']);
    Route::post('/books/{book}/toggle-visibility', [BookController::class, 'toggleVisibility']);
    
    // Lending
    Route::get('/books/borrowed', [BookController::class, 'apiBorrowedBooks']);
    Route::post('/books/assign', [BookController::class, 'assign']);
    Route::post('/books/return', [BookController::class, 'returnBook']);
    Route::post('/books/change-shelf', [BookController::class, 'changeShelf']);
    Route::post('/books/transfer', [BookController::class, 'transfer']);

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{book}', [WishlistController::class, 'destroy']);

    // Ratings
    Route::post('/books/{book}/ratings', [RatingController::class, 'store']);
    Route::get('/books/{book}/ratings/user', [RatingController::class, 'getUserRating']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
