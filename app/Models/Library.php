<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Library extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'location',
        'invite_token',
        'description',
        'image',
        'owner_id',
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Boot method to generate invite token on creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($library) {
            if ($library->type === 'private' && empty($library->invite_token)) {
                $library->invite_token = Str::random(32);
            }
        });

        static::updating(function ($library) {
            if ($library->type === 'private' && empty($library->invite_token)) {
                $library->invite_token = Str::random(32);
            }
        });
    }

    /**
     * Get the owner of the library
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * A library has many rooms
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * A library has many shelves through rooms
     */
    public function shelves()
    {
        return $this->hasManyThrough(Shelf::class, Room::class);
    }

    /**
     * A library has many books through shelves
     */
    public function books()
    {
        return $this->hasManyThrough(Book::class, Shelf::class);
    }

    /**
     * Get the members of the library
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'library_user')
            ->withTimestamps();
    }

    /**
     * Get invitations for this library
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Check if library is private
     */
    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    /**
     * Check if library is public
     */
    public function isPublic(): bool
    {
        return $this->type === 'public';
    }

    /**
     * Validate invite token
     */
    public function validateInviteToken(string $token): bool
    {
        return $this->isPrivate() && hash_equals($this->invite_token, $token);
    }

    /**
     * Get total books count
     */
    public function getTotalBooksAttribute(): int
    {
        return $this->books()->count();
    }

    /**
     * Get total rooms count
     */
    public function getTotalRoomsAttribute(): int
    {
        return $this->rooms()->count();
    }

    /**
     * Get total shelves count
     */
    public function getTotalShelvesAttribute(): int
    {
        return $this->shelves()->count();
    }
}
