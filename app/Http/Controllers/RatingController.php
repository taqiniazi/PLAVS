<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Store or update a rating for a book.
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if user already has a rating for this book
        $rating = Rating::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ],
            [
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );

        // Calculate new average rating
        $averageRating = $book->ratings()->avg('rating');
        $ratingCount = $book->ratings()->count();

        return response()->json([
            'success' => true,
            'message' => 'Rating saved successfully',
            'rating' => $rating->rating,
            'review' => $rating->review,
            'average_rating' => round($averageRating, 1),
            'rating_count' => $ratingCount,
            'user_rating' => $rating->rating,
        ]);
    }

    /**
     * Get the current user's rating for a book.
     */
    public function getUserRating(Book $book)
    {
        $user = Auth::user();

        $rating = Rating::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        return response()->json([
            'rating' => $rating ? $rating->rating : null,
            'review' => $rating ? $rating->review : null,
        ]);
    }
}
