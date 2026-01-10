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
    ];

    /**
     * Role constants
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_LIBRARIAN = 'librarian';
    public const ROLE_OWNER = 'owner';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';

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

    /**
     * Get the libraries this user has access to
     */
    public function libraries()
    {
        // Owners see their own libraries, admins see all
        if ($this->isAdmin()) {
            return Library::query();
        }
        
        return $this->ownedLibraries();
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if user is Admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    /**
     * Check if user is Librarian
     */
    public function isLibrarian(): bool
    {
        return $this->role === self::ROLE_LIBRARIAN;
    }

    /**
     * Check if user is Owner
     */
    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    /**
     * Check if user is Teacher
     */
    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    /**
     * Check if user is Student
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Check if user has administrative role
     */
    public function hasAdminRole(): bool
    {
        return in_array($this->role, self::ADMIN_ROLES);
    }

    /**
     * Check if user can manage libraries
     */
    public function canManageLibraries(): bool
    {
        return $this->hasAdminRole();
    }

    /**
     * Check if user can assign books
     */
    public function canAssignBooks(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_LIBRARIAN, self::ROLE_TEACHER]);
    }

    /**
     * Check if user can view all books
     */
    public function canViewAllBooks(): bool
    {
        return $this->hasAdminRole();
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_LIBRARIAN => 'Librarian',
            self::ROLE_OWNER => 'Library Owner',
            self::ROLE_TEACHER => 'Teacher',
            self::ROLE_STUDENT => 'Student',
            default => ucfirst($this->role),
        };
    }

    /**
     * Get students assigned to this teacher
     */
    public function assignedStudents()
    {
        return $this->belongsToMany(User::class, 'book_user', 'user_id', 'book_id')
            ->where('assignment_type', 'teacher_assign')
            ->whereHas('books', function ($query) {
                $query->whereIn('book_user.user_id', [$this->id]);
            });
    }

    /**
     * Get teachers who assigned books to this student
     */
    public function assignedByTeachers()
    {
        return $this->belongsToMany(User::class, 'book_user', 'user_id', 'book_id')
            ->where('assignment_type', 'teacher_assign');
    }

    /**
     * Get the user's wishlist items.
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the user's ratings.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
