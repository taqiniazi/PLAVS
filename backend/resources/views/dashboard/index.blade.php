@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Dashboard')

@section('content')
@php
    $user = auth()->user();
    $isTeacher = $user->isTeacher();
    $isStudent = $user->isStudent();
    $hasAdminRole = $user->hasAdminRole();
@endphp

<div class="row">
    {{-- Admin/Librarian/Owner Stats --}}
    @if($hasAdminRole)
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Total Libraries</small>
                    <h4 class="fw-bold mb-0">{{ $stats['total_libraries'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/total_books.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Total Books</small>
                    <h4 class="fw-bold mb-0">{{ $stats['total_books'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Book Shelves</small>
                    <h4 class="fw-bold mb-0">{{ $stats['book_shelves'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Active Members</small>
                    <h4 class="fw-bold mb-0">{{ $stats['active_members'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/users_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    @elseif($isTeacher)
        {{-- Teacher Stats --}}
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">My Students</small>
                    <h4 class="fw-bold mb-0">{{ $stats['my_students'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/users_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Books Assigned</small>
                    <h4 class="fw-bold mb-0">{{ $stats['books_assigned'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/total_books.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Available Books</small>
                    <h4 class="fw-bold mb-0">{{ $stats['available_books'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">My Library</small>
                    <h4 class="fw-bold mb-0">{{ $stats['my_libraries'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/total_books.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    @elseif($isStudent)
        {{-- Student Stats --}}
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">My Assigned Books</small>
                    <h4 class="fw-bold mb-0">{{ $stats['my_assigned_books'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/total_books.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Books Read</small>
                    <h4 class="fw-bold mb-0">{{ $stats['books_read'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_borrowed_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Currently Reading</small>
                    <h4 class="fw-bold mb-0">{{ $stats['currently_reading'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Teachers</small>
                    <h4 class="fw-bold mb-0">{{ $stats['my_teachers'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/users_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    @else
        {{-- Default Stats for other roles --}}
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Total Books</small>
                    <h4 class="fw-bold mb-0">{{ $stats['total_books'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/total_books.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Active Members</small>
                    <h4 class="fw-bold mb-0">{{ $stats['active_members'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/users_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Books Borrowed</small>
                    <h4 class="fw-bold mb-0">{{ $stats['books_borrowed'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_borrowed_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Book Shelves</small>
                    <h4 class="fw-bold mb-0">{{ $stats['book_shelves'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    @endif
</div>

<div class="row mt-3">
    {{-- Show different content based on role --}}
    
    {{-- Admin/Librarian/Owner: Libraries and Recently Added --}}
    @if($hasAdminRole)
        <div class="col-lg-9">
            <h6 class="fw-bold mb-3">Libraries Overview</h6>
            <div class="table-card mb-3">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Library Name</th>
                                <th>Type</th>
                                <th>Rooms</th>
                                <th>Shelves</th>
                                <th>Books</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($libraries ?? [] as $library)
                            <tr>
                                <td>
                                    <i class="fas fa-building me-2"></i>
                                    {{ $library->name }}
                                </td>
                                <td>
                                    <span class="badge {{ $library->type === 'public' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($library->type) }}
                                    </span>
                                </td>
                                <td>{{ $library->rooms->count() }}</td>
                                <td>{{ $library->shelves->count() }}</td>
                                <td>{{ $library->books->count() }}</td>
                                <td>
                                    <a href="{{ route('libraries.show', $library) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-building fa-2x text-muted mb-2 d-block"></i>
                                    No libraries found. Create your first library!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <h6 class="fw-bold mb-3">Recently Added Books</h6>
            <div class="row">
                @foreach($recent_books as $book)
                <div class="col-md-3 col-6 mb-3">
                    <div class="book-card">
                        <img src="{{ $book['image'] }}" alt="{{ $book['title'] }}" class="img-fluid">
                        <p class="book-title">{{ $book['title'] }}</p>
                        <span class="book-author">Author : {{ $book['author'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @elseif($isTeacher)
        {{-- Teacher: Students and Assignments --}}
        <div class="col-lg-9">
            <h6 class="fw-bold mb-3">My Students</h6>
            <div class="table-card mb-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Books Assigned</th>
                                <th>Last Activity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($my_students ?? [] as $student)
                            <tr>
                                <td>
                                    <i class="fas fa-user-graduate me-2"></i>
                                    {{ $student->name }}
                                </td>
                                <td>{{ $student->books->count() }}</td>
                                <td>{{ $student->updated_at->diffForHumans() }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-book"></i> Assign Book
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="fas fa-users fa-2x text-muted mb-2 d-block"></i>
                                    No students assigned yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <h6 class="fw-bold mb-3">Recently Assigned Books</h6>
            <div class="row">
                @forelse($recently_assigned ?? [] as $assignment)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">{{ $assignment->book->title }}</h6>
                            <p class="card-text small text-muted">
                                Assigned to: {{ $assignment->user->name }}
                            </p>
                            <small class="text-muted">
                                {{ $assignment->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-book fa-2x mb-2 d-block"></i>
                        No books assigned yet.
                    </p>
                </div>
                @endforelse
            </div>
        </div>
    @elseif($isStudent)
        {{-- Student: Assigned Books --}}
        <div class="col-lg-9">
            <h6 class="fw-bold mb-3">My Assigned Books</h6>
            <div class="row">
                @forelse($my_assigned_books ?? [] as $book)
                <div class="col-md-4 mb-3">
                    <div class="book-card h-100">
                        <img src="{{ $book->cover_image ?? asset('images/book1.png') }}" 
                             alt="{{ $book->title }}" class="img-fluid">
                        <p class="book-title">{{ $book->title }}</p>
                        <span class="book-author">Author : {{ $book->author }}</span>
                        <div class="mt-2">
                            <span class="badge {{ $book->status === 'Available' ? 'bg-success' : 'bg-warning' }}">
                                {{ $book->status }}
                            </span>
                        </div>
                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No books assigned to you yet.</p>
                        <a href="{{ route('books.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Library
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    @else
        {{-- Default: Recently Added Books --}}
        <div class="col-lg-9">
            <h6 class="fw-bold mb-3">Recently Added</h6>
            <div class="row">
                @foreach($recent_books as $book)
                <div class="col-md-3 col-6 mb-3">
                    <div class="book-card">
                        <img src="{{ $book['image'] }}" alt="{{ $book['title'] }}" class="img-fluid">
                        <p class="book-title">{{ $book['title'] }}</p>
                        <span class="book-author">Author : {{ $book['author'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="col-lg-3">
        <h6 class="fw-bold mb-3">Recent Activities</h6>
        <div class="bg-white p-3 rounded-3 border-0">
            @foreach($recent_activities as $activity)
            <div class="timeline-item">
                <div class="timeline-dot active"></div>
                <div>
                    <p class="mb-0 small fw-bold">{{ $activity['type'] }}</p>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $activity['description'] }}</small>
                    <br>
                    <small class="text-muted" style="font-size: 0.7rem;">{{ $activity['time'] }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
