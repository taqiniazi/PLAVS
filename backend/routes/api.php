<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\BookController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/libraries', [LibraryController::class, 'apiIndex']);
Route::get('/libraries/{library}', [LibraryController::class, 'apiShow']);
Route::get('/libraries/{library}/books', [LibraryController::class, 'apiBooks']);

Route::get('/books', [BookController::class, 'apiIndex']);
Route::get('/books/{book}', [BookController::class, 'details']);
