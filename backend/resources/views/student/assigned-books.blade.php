@extends('layouts.dashboard')

@section('title', 'My Assigned Books')

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header">
            <h4 class="page-title">My Assigned Books</h4>
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
        
        @if($assignedBooks->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Assigned Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <!-- <th style="width: 150in px;">Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignedBooks as $book)
                    @php
                        $pivot = $book->pivot;
                        $isReturned = $pivot->is_returned ?? false;
                        $assignedAt = $pivot->assigned_at ? \Carbon\Carbon::parse($pivot->assigned_at)->format('M d, Y') : 'N/A';
                        $returnDate = $pivot->return_date ? \Carbon\Carbon::parse($pivot->return_date)->format('M d, Y') : '-';
                    @endphp
                    <tr class="{{ $isReturned ? 'table-secondary' : '' }}" style="{{ $isReturned ? 'opacity: 0.7;' : '' }}">
                        <td>
                            <img src="{{ $book->cover_url }}"
                                 alt="{{ $book->title }}"
                                 class="img-thumbnail"
                                 style="width: 60px; height: 80px; object-fit: cover;">
                        </td>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="text-decoration-none {{ $isReturned ? 'text-muted' : 'text-dark' }}">
                                {{ $book->title }}
                            </a>
                        </td>
                        <td>{{ $book->author }}</td>
                        <td>{{ $assignedAt }}</td>
                        <td>{{ $returnDate }}</td>
                        <td>
                            @if($isReturned)
                            <span class="badge bg-secondary">Returned</span>
                            @else
                            <span class="badge bg-success">In Use</span>
                            @endif
                        </td>
                        <!-- <td>
                            @if(!$isReturned)
                            <form action="{{ route('student.return-book', $book) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Return Book">
                                    <i class="fas fa-undo"></i> Return
                                </button>
                            </form>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td> -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No books have been assigned to you yet.</h5>
            <p class="text-muted">Check back later or contact your teacher/librarian.</p>
        </div>
        @endif
    </div>
</div>
@endsection
