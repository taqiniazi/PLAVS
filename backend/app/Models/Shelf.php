<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'room_id'
    ];

    /**
     * Get the room that owns the shelf
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the library through room
     */
    public function library()
    {
        return $this->room->library();
    }

    /**
     * A shelf has many books
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Get total books count
     */
    public function getTotalBooksAttribute(): int
    {
        return $this->books()->count();
    }
}
