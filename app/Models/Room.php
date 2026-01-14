<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'floor',
        'library_id'
    ];

    /**
     * Get the library that owns the room
     */
    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    /**
     * A room has many shelves
     */
    public function shelves()
    {
        return $this->hasMany(Shelf::class);
    }

    /**
     * A room has many books through shelves
     */
    public function books()
    {
        return $this->hasManyThrough(Book::class, Shelf::class);
    }

    /**
     * Get total books count
     */
    public function getTotalBooksAttribute(): int
    {
        return $this->books()->count();
    }

    /**
     * Get total shelves count
     */
    public function getTotalShelvesAttribute(): int
    {
        return $this->shelves()->count();
    }
}
