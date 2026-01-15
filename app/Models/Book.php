<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'edition',
        'publisher',
        'publish_date',
        'shelf_location',
        'owner',
        'assigned_user_id',
        'description',
        'visibility',
        'status',
        'image',
        'cover_image',
        'shelf_id',
        'category_id',
    ];

    protected $casts = [
        'visibility' => 'boolean',
        'publish_date' => 'date',
    ];

    /**
     * Get the shelf that owns the book
     */
    public function shelf()
    {
        return $this->belongsTo(Shelf::class);
    }

    /**
     * Get the room through shelf
     */
    public function room()
    {
        return $this->shelf->room();
    }

    /**
     * Get the library through shelf/room
     */
    public function library()
    {
        return $this->shelf->room->library();
    }

    /**
     * Get the category of the book
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who owns this book
     */
    public function ownedBy()
    {
        return $this->belongsTo(User::class, 'owner', 'name');
    }

    /**
     * Get the user this book is assigned to
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Get all users this book is assigned to (through pivot)
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'book_user')
            ->withPivot(['assignment_type', 'notes', 'assigned_at'])
            ->withTimestamps();
    }

    /**
     * Check if book is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'Available';
    }

    /**
     * Check if book is visible
     */
    public function isVisible(): bool
    {
        return $this->visibility === true;
    }

    /**
     * Get full location string
     */
    public function getFullLocationAttribute(): string
    {
        if (! $this->shelf) {
            return 'Not assigned';
        }

        return $this->shelf->room->library->name.' > '.
               $this->shelf->room->name.' > '.
               $this->shelf->name;
    }

    /**
     * Get users who have this book in their wishlist
     */
    public function wishlistUsers()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get ratings for this book
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get average rating for this book
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get rating count for this book
     */
    public function getRatingCountAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Get the cover URL for this book
     * Handles both local storage paths and external URLs (Google Books API)
     */
    public function getCoverUrlAttribute()
    {
        // If it's already a full URL (Google Books API), return as-is
        if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            return $this->cover_image;
        }

        // If it's a local file in storage, return the asset URL
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        // Fallback to default book cover
        return asset('images/book1.png');
    }
}
