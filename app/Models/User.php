<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
        'parent_owner_id',
        'requested_owner',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'requested_owner' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Role constants
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_LIBRARIAN = 'librarian';
    public const ROLE_OWNER = 'owner';
    public const ROLE_PUBLIC = 'public';

    /**
     * Administrative roles
     */
    public const ADMIN_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_LIBRARIAN,
        self::ROLE_OWNER,
    ];

    /**
     * Get all owned books
     */
    public function ownedBooks()
    {
        return $this->hasMany(Book::class, 'owner', 'name');
    }

    /**
     * Get books directly assigned to this user
     */
    public function assignedBooks()
    {
        return $this->hasMany(Book::class, 'assigned_user_id');
    }

    /**
     * Get books assigned through pivot table
     */
    public function booksThroughAssignment()
    {
        return $this->belongsToMany(Book::class, 'book_user')
            ->withPivot(['assignment_type', 'notes', 'assigned_at', 'return_date', 'is_returned', 'return_notes'])
            ->withTimestamps();
    }

    /**
     * Get all accessible books (assigned + through pivot)
     */
    public function accessibleBooks()
    {
        return $this->assignedBooks()->union($this->booksThroughAssignment());
    }

    /**
     * Get non-returned books assigned through pivot
     */
    public function activeAssignedBooks()
    {
        return $this->booksThroughAssignment()
            ->wherePivot('is_returned', false);
    }

    /**
     * Get all libraries owned by this user
     */
    public function ownedLibraries()
    {
        return $this->hasMany(Library::class, 'owner_id');
    }

    public function ownerRequests()
    {
        return $this->hasMany(OwnerRequest::class);
    }

    // Link librarian to their parent owner
    public function parentOwner()
    {
        return $this->belongsTo(User::class, 'parent_owner_id');
    }

    /**
     * Get libraries the user has joined as a member
     */
    public function joinedLibraries()
    {
        return $this->belongsToMany(Library::class, 'library_user')
            ->withTimestamps();
    }

    /**
     * Get the libraries this user has access to
     */
    public function libraries()
    {
        // Owners see their own libraries, admins see all
        if ($this->isAdmin()) {
            return Library::query();
        }
        
        // Librarians see libraries of their parent owner
        if ($this->isLibrarian()) {
             return Library::where('owner_id', $this->parent_owner_id);
        }

        // Public users see joined libraries + owned libraries (if any)
        if ($this->isStudent()) {
             return $this->joinedLibraries();
        }

        return $this->ownedLibraries();
    }

    /**
     * Normalize arbitrary role value to canonical key (lowercase, underscores, with aliases)
     */
    private function normalizeRoleValue(string $role): string
    {
        $raw = strtolower(trim($role));
        $norm = preg_replace('/[\s\-]+/', '_', $raw); // spaces or hyphens to underscores
        $aliases = [
            'superadmin' => 'super_admin',
            'student' => 'public',
            'candidate' => 'public',
        ];
        return $aliases[$norm] ?? $norm;
    }

    /**
     * Normalize current user's role to canonical key
     */
    private function roleKey(): string
    {
        return $this->normalizeRoleValue((string) $this->role);
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->roleKey() === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if user is Admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->roleKey(), [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN], true);
    }

    /**
     * Check if user is Librarian
     */
    public function isLibrarian(): bool
    {
        return $this->roleKey() === self::ROLE_LIBRARIAN;
    }

    /**
     * Check if user is Owner
     */
    public function isOwner(): bool
    {
        return $this->roleKey() === self::ROLE_OWNER;
    }

    public function isPublic(): bool
    {
        return $this->roleKey() === self::ROLE_PUBLIC;
    }

    public function isStudent(): bool
    {
        return $this->isPublic();
    }

    /**
     * Check if user has administrative role
     */
    public function hasAdminRole(): bool
    {
        return in_array($this->roleKey(), self::ADMIN_ROLES, true);
    }

    /**
     * Check if user can assign books
     */
    public function canAssignBooks(): bool
    {
        return in_array($this->roleKey(), [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_LIBRARIAN], true);
    }

    /**
     * Check if user can view all books
     */
    public function canViewAllBooks(): bool
    {
        // Librarians should NOT see all system books; limit to their owner's libraries
        return in_array($this->roleKey(), [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_OWNER], true);
    }

    /**
     * Check if the user has a specific role.
     * Accepts either display strings like 'Super Admin' or keys like 'super_admin'.
     */
    public function hasRole($role)
    {
        $key = $this->normalizeRoleValue(is_string($role) ? $role : (string) $role);
        return $this->roleKey() === $key;
    }

    // Helper for checking multiple roles
    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            $normalized = array_map(function ($r) {
                return $this->normalizeRoleValue((string) $r);
            }, $roles);
            return in_array($this->roleKey(), $normalized, true);
        }
        $key = $this->normalizeRoleValue((string) $roles);
        return $this->roleKey() === $key;
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        $key = $this->roleKey();
        return match($key) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_LIBRARIAN => 'Librarian',
            self::ROLE_OWNER => 'Library Owner',
            self::ROLE_PUBLIC => 'Public',
            default => ucwords(str_replace('_', ' ', $key)),
        };
    }

    /**
     * Get the user's wishlist items.
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the user's wishlist books.
     */
    public function wishlistBooks()
    {
        return $this->belongsToMany(Book::class, 'wishlists', 'user_id', 'book_id')
            ->withTimestamps();
    }

    /**
     * Get the user's ratings.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Duplicate legacy hasRole/hasAnyRole methods were removed.
    // Normalized versions are defined earlier in the class.
    // Legacy duplicate methods removed to prevent redeclare errors.
    // hasRole() and hasAnyRole() are already defined above with normalized role checks.
}
