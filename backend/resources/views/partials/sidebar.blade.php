<div class="sidebar d-flex flex-column" id="sidebar">
    <button class="btn btn-small btn-dark d-lg-none px-2 py-0 position-absolute" style="right: 8px; top: 8px;" onclick="document.getElementById('sidebar').classList.remove('show')"><i class="fa fa-times"></i></button>
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" class="img-fluid">
    </div>
    <nav class="nav flex-column">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        
       

        {{-- Administrative Links: Librarian/Admin/Super Admin/Owner --}}
        @if(auth()->user()->hasAdminRole())
         {{-- All users can view books --}}
            <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.index') ? 'active' : '' }}">
                <i class="fas fa-search"></i> View Books
            </a>
            <a href="{{ route('books.manage') }}" class="nav-link {{ request()->routeIs('books.manage') ? 'active' : '' }}">
                <i class="fas fa-box-open"></i> Manage Books
            </a>
            
            {{-- Library Management --}}
            <a href="{{ route('libraries.index') }}" class="nav-link {{ request()->routeIs('libraries.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Libraries
            </a>
            
            {{-- Shelves Management --}}
            <a href="{{ route('shelves.index') }}" class="nav-link {{ request()->routeIs('shelves.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Manage Shelves
            </a>
        @endif

        {{-- Owner Links --}}
        @if(auth()->user()->isOwner())
            <!-- <a href="{{ route('libraries.index') }}" class="nav-link {{ request()->routeIs('libraries.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i> My Libraries
            </a> -->
            <a href="{{ route('rooms.index') }}" class="nav-link {{ request()->routeIs('rooms.index') ? 'active' : '' }}">
                <i class="fas fa-door-open"></i> My Rooms
            </a>
            <!-- <a href="{{ route('shelves.index') }}" class="nav-link {{ request()->routeIs('shelves.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Manage Shelves
            </a> -->
        @endif
            
            {{-- Administrative Links: Librarian/Admin/Super Admin --}}
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isLibrarian())
                {{-- All users can view books --}}
                <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.index') ? 'active' : '' }}">
                    <i class="fas fa-search"></i> View Books
                </a>
                <a href="{{ route('books.manage') }}" class="nav-link {{ request()->routeIs('books.manage') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i> Manage Books
                </a>
                
                {{-- Library Management --}}
                <a href="{{ route('libraries.index') }}" class="nav-link {{ request()->routeIs('libraries.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Libraries
                </a>
                
                {{-- Shelves Management --}}
                <a href="{{ route('shelves.index') }}" class="nav-link {{ request()->routeIs('shelves.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Manage Shelves
                </a>
            @endif
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.index') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Owners
                </a>
            @endif

        {{-- Teacher specific links --}}
        @if(auth()->user()->isTeacher())
            <a href="{{ route('teachers.students') }}" class="nav-link {{ request()->routeIs('teachers.students') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i> My Students
            </a>
            <a href="{{ route('teachers.assignments') }}" class="nav-link {{ request()->routeIs('teachers.assignments') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i> Book Assignments
            </a>
        @endif

        {{-- Student specific links --}}
        @if(auth()->user()->isStudent())
            <a href="{{ route('student.assigned-books') }}" class="nav-link {{ request()->routeIs('student.assigned-books') ? 'active' : '' }}">
                <i class="fas fa-book-reader"></i> Assigned Books
            </a>
        @endif

        {{-- Events - visible to all authenticated users --}}
        <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
    </nav>
</div>
