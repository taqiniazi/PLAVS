@extends('layouts.dashboard')

@section('title', 'Transferred / Lent Books')

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header">
            <h4 class="page-title">Transferred / Lent Books</h4>
            <div class="header-actions">
                <a href="{{ route('books.manage') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Manage Books
                </a>
            </div>
        </div>
        
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Search Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('books.transferred') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search books or recipients..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i> Search
                        </button>
                        <a href="{{ route('books.transferred') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-refresh me-2"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        @if($transferredBooks->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Current Holder</th>
                        <th>Transfer Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transferredBooks as $book)
                    <tr>
                        <td>
                            <img src="{{ $book->cover_url }}"
                                 alt="{{ $book->title }}"
                                 class="img-thumbnail"
                                 style="width: 60px; height: 80px; object-fit: cover;">
                        </td>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="text-decoration-none text-dark fw-medium">
                                {{ $book->title }}
                            </a>
                        </td>
                        <td>{{ $book->author }}</td>
                        <td>
                            @if($book->assigned_user_id)
                                <span class="badge bg-primary">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $book->assignedUser->name }}
                                </span>
                                <small class="text-muted d-block">{{ $book->assignedUser->role }}</small>
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $book->shelf->room->library->name }}
                                </span>
                                <small class="text-muted d-block">Transferred</small>
                            @endif
                        </td>
                        <td>
                            @if($book->assigned_user_id)
                                <span class="text-muted">
                                    {{ $book->created_at->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-muted">
                                    {{ $book->updated_at->format('M d, Y') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($book->assigned_user_id)
                                <span class="badge bg-info">
                                    <i class="fas fa-handshake me-1"></i>
                                    Assigned
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-exchange-alt me-1"></i>
                                    Transferred
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($book->assigned_user_id)
                                <form action="{{ route('books.recall', $book) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                            onclick="return confirm('Are you sure you want to recall this book from {{ $book->assignedUser->name }}?')">
                                        <i class="fas fa-undo me-1"></i> Recall
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No transferred or lent books found</h5>
            <p class="text-muted">Books that have been transferred to other libraries or assigned to users will appear here.</p>
            <a href="{{ route('books.manage') }}" class="btn btn-primary">
                <i class="fas fa-book me-2"></i> Manage Books
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation for recall actions
    const recallForms = document.querySelectorAll('form[action*="recall"]');
    recallForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const userName = form.querySelector('button').getAttribute('onclick').match(/'([^']+)'/)[1];
            if (!confirm('Are you sure you want to recall this book from ' + userName + '?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush