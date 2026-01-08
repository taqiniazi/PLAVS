<div class="sidebar d-flex flex-column" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" class="img-fluid">
    </div>
    <nav class="nav flex-column">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.index') ? 'active' : '' }}">
            <i class="fas fa-search"></i> View Books
        </a>
        <a href="{{ route('books.manage') }}" class="nav-link {{ request()->routeIs('books.manage') ? 'active' : '' }}">
            <i class="fas fa-box-open"></i> Manage Books
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-book"></i> Book Shelves
        </a>
        <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.index') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Owners
        </a>
        <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
    </nav>
</div>