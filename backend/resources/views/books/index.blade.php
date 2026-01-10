@extends('layouts.dashboard')

@section('title', 'MyBookShelf - View Books')

@section('content')
@php
    $user = auth()->user();
    $canAddBooks = $user && ($user->hasAdminRole() || $user->isTeacher());
    $isStudent = $user && !$user->canViewAllBooks();
    $totalBooks = isset($books) && is_object($books) && method_exists($books, 'total') ? $books->total() : (is_countable($books) ? count($books) : 0);
@endphp
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header">
            <h4 class="page-title">{{ $isStudent ? 'My Assigned Books' : 'Digital Library' }}</h4>
            
            <div class="header-actions">
                @if(!$isStudent)
                <form method="GET" action="{{ route('books.index') }}" class="d-flex align-items-center gap-3">
                    <div class="custom-search">
                        <input type="text" name="search" placeholder="Search books..." 
                               value="{{ request('search') }}" onchange="this.form.submit()">
                        <i class="fas fa-search"></i>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </form>
                @endif
                @if($canAddBooks)
                <a href="{{ route('books.create') }}" class="btn btn-gold">Add New Book</a>
                @endif
            </div>
        </div>
        
        @if(request('search') && !$isStudent)
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-search me-2"></i>
                    Showing {{ $books->count() }} of {{ $totalBooks }} results for "<strong>{{ request('search') }}</strong>"
                </div>
            </div>
        </div>
        @endif
        
        @if($isStudent && request('search'))
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-search me-2"></i>
                    Found {{ count($books) }} assigned books matching "<strong>{{ request('search') }}</strong>"
                </div>
            </div>
        </div>
        @endif
        
        <div class="library-grid">
            @forelse($books as $book)
            <div class="book-item mb-3">
                <a href="{{ route('books.show', $book) }}" class="book-card text-decoration-none">
                    <img src="{{ $book->cover_url }}"
                         alt="{{ $book->title }}" class="img-fluid">
                    <p class="book-title">{{ strlen($book->title) > 18 ? substr($book->title, 0, 15) . '...' : $book->title }}</p>
                    <span class="book-author">Author : {{ $book->author }}</span>
                </a>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        @if(request('search'))
                            No books found matching "{{ request('search') }}"
                        @elseif($isStudent)
                            No books have been assigned to you yet.
                        @else
                            No books available
                        @endif
                    </h5>
                    @if(request('search'))
                        <a href="{{ route('books.index') }}" class="btn btn-primary mt-2">View All Books</a>
                    @elseif($isStudent)
                        <p class="text-muted">Check back later or contact your teacher/librarian.</p>
                    @elseif($canAddBooks)
                        <a href="{{ route('books.create') }}" class="btn btn-primary mt-2">Add Your First Book</a>
                    @endif
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if(isset($books) && is_object($books) && method_exists($books, 'hasPages') && $books->hasPages())
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $books->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = searchInput ? searchInput.closest('form') : null;
    
    if (searchInput && searchForm) {
        let searchTimeout;

        // Auto-submit search after user stops typing (debounced)
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
                    searchForm.submit();
                }
            }, 500); // Wait 500ms after user stops typing
        });

        // Submit on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                searchForm.submit();
            }
        });
    }
});
</script>
@endpush