<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', [AuthController::class, 'apiRegister']);
Route::post('/auth/login', [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'apiLogout']);

    Route::get('/profile', [ProfileController::class, 'apiShow']);
    Route::put('/profile', [ProfileController::class, 'apiUpdate']);
    Route::put('/profile/password', [ProfileController::class, 'apiUpdatePassword']);
});

Route::get('/libraries', [LibraryController::class, 'apiIndex']);
Route::get('/libraries/{library}', [LibraryController::class, 'apiShow']);
Route::get('/libraries/{library}/books', [LibraryController::class, 'apiBooks']);

Route::get('/books', [BookController::class, 'apiIndex']);
Route::get('/books/{book}', [BookController::class, 'details']);
Route::post('/books', [BookController::class, 'store'])->middleware('auth:sanctum');
Route::put('/books/{book}', [BookController::class, 'update'])->middleware('auth:sanctum');
Route::patch('/books/{book}', [BookController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/books/{book}', [BookController::class, 'destroy'])->middleware('auth:sanctum');

Route::post('/books/{book}/toggle-visibility', [BookController::class, 'toggleVisibility'])->middleware('auth:sanctum');
Route::post('/books/assign', [BookController::class, 'assign'])->middleware('auth:sanctum');
Route::post('/books/return', [BookController::class, 'returnBook'])->middleware('auth:sanctum');
Route::post('/books/change-shelf', [BookController::class, 'changeShelf'])->middleware('auth:sanctum');
Route::post('/books/transfer', [BookController::class, 'transfer'])->middleware('auth:sanctum');
