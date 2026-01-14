<div class="sidebar d-flex flex-column" id="sidebar">
    <button class="btn btn-small btn-dark d-lg-none px-2 py-0 position-absolute" style="right: 8px; top: 8px;" onclick="document.getElementById('sidebar').classList.remove('show')"><i class="fa fa-times"></i></button>
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" class="img-fluid">
    </div>
    <nav class="nav flex-column">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        {{-- Administrative Links: Super Admin / Admin / Librarian / Owner --}}
        @if(auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'superadmin', 'Super Admin']))
            <!-- <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.index') ? 'active' : '' }}">
                <i class="fas fa-search"></i> View Books
            </a> -->
            <a href="{{ route('books.manage') }}" class="nav-link {{ request()->routeIs('books.manage') ? 'active' : '' }}">
                <i class="fas fa-box-open"></i> Manage Books
            </a>
            <a href="{{ route('libraries.index') }}" class="nav-link {{ request()->routeIs('libraries.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Libraries
            </a>
            <a href="{{ route('shelves.index') }}" class="nav-link {{ request()->routeIs('shelves.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Manage Shelves
            </a>
            <a href="{{ route('rooms.index') }}" class="nav-link {{ request()->routeIs('rooms.index') ? 'active' : '' }}">
                <i class="fas fa-door-open"></i> Rooms
            </a>
            <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.index') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Owners
            </a>
            <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.index') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i> Permissions
            </a>
            <!-- Teacher-specific links removed -->
            <a href="{{ route('public.assigned-books') }}" class="nav-link {{ request()->routeIs('public.assigned-books') ? 'active' : '' }}">
                <i class="fas fa-book-reader"></i> Assigned Books
            </a>
        @else
            @php $user = auth()->check() ? auth()->user() : null; @endphp

            {{-- All users can view books --}}
            <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.index') ? 'active' : '' }}">
                <i class="fas fa-search"></i> View Books
            </a>
            @if($user && ($user->isOwner() || $user->isLibrarian() || $user->isAdmin()))
                <a href="{{ route('books.manage') }}" class="nav-link {{ request()->routeIs('books.manage') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i> Manage Books
                </a>
            @endif

            {{-- Library Management (view) --}}
            <a href="{{ route('libraries.index') }}" class="nav-link {{ request()->routeIs('libraries.*') && !request()->routeIs('libraries.other*') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Libraries
            </a>

            {{-- Other Libraries (Owner/Librarian only) --}}
            @if($user && ($user->isOwner() || $user->isLibrarian()))
                <a href="{{ route('libraries.other') }}" class="nav-link {{ request()->routeIs('libraries.other*') ? 'active' : '' }}">
                    <i class="fas fa-globe"></i> Other Libraries
                </a>
            @endif

            {{-- Shelves Management (owner/librarian only) --}}
            @if($user && ($user->isOwner() || $user->isLibrarian()))
                <a href="{{ route('shelves.index') }}" class="nav-link {{ request()->routeIs('shelves.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Manage Shelves
                </a>
            @endif

            {{-- Owner specific links --}}
            @if($user && $user->isOwner())
                <a href="{{ route('rooms.index') }}" class="nav-link {{ request()->routeIs('rooms.index') ? 'active' : '' }}">
                    <i class="fas fa-door-open"></i> My Rooms
                </a>
            @endif

            {{-- Permissions (Admins/Superadmins) --}}
            @if($user && ($user->isSuperAdmin() || $user->isAdmin()))
                <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.index') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i> Permissions
                </a>
                <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.index') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Owners
                </a>
            @endif

            {{-- Teacher specific links removed --}}

            {{-- Public specific links --}}
            @if($user && $user->isPublic())
                <a href="{{ route('public.assigned-books') }}" class="nav-link {{ request()->routeIs('public.assigned-books') ? 'active' : '' }}">
                    <i class="fas fa-book-reader"></i> Assigned Books
                </a>
            @endif
        @endif

        {{-- Events - visible to all authenticated users --}}
        <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
    </nav>
</div>
