<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Book::class => \App\Policies\BookPolicy::class,
        \App\Models\Library::class => \App\Policies\LibraryPolicy::class,
        \App\Models\Room::class => \App\Policies\RoomPolicy::class,
        \App\Models\Shelf::class => \App\Policies\ShelfPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Gates for role-based access control
        
        // Super Admin can do everything
        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        // Admin roles can manage libraries, books, rooms, shelves
        Gate::define('manage-libraries', function ($user) {
            return $user->hasAdminRole();
        });

        Gate::define('manage-books', function ($user) {
            return $user->hasAdminRole();
        });

        Gate::define('assign-books', function ($user) {
            return $user->canAssignBooks();
        });

        Gate::define('view-all-books', function ($user) {
            return $user->canViewAllBooks();
        });
    }
}
