<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $user = Auth::user();
        $wishlistBooks = $user->wishlist()->with('book.shelf.room.library')->get();

        return view('wishlist.index', compact('wishlistBooks'));
    }

    /**
     * Toggle a book in/out of the user's wishlist.
     */
    public function toggle(Book $book)
    {
        $user = Auth::user();
        
        // Check if book is already in wishlist
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($wishlistItem) {
            // Remove from wishlist
            $wishlistItem->delete();
            $isInWishlist = false;
            $message = 'Book removed from wishlist';
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);
            $isInWishlist = true;
            $message = 'Book added to wishlist';
        }

        return response()->json([
            'success' => true,
            'is_in_wishlist' => $isInWishlist,
            'message' => $message,
            'button_text' => $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist',
            'button_class' => $isInWishlist ? 'btn-danger' : 'btn-primary',
            'icon_class' => $isInWishlist ? 'fa-heart' : 'fa-heart-o'
        ]);
    }
}