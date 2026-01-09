@extends('layouts.dashboard')

@section('title', 'My Assigned Books')

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header">
            <h4 class="page-title">My Assigned Books</h4>
        </div>
        
        @if($assignedBooks->count() > 0)
        <div class="library-grid">
            @foreach($assignedBooks as $book)
            <div class="book-item mb-3">
                <a href="{{ route('books.show', $book) }}" class="book-card text-decoration-none">
                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/' . ($book->image ?? 'book1.png')) }}" 
                         alt="{{ $book->title }}" class="img-fluid">
                    <p class="book-title">{{ strlen($book->title) > 18 ? substr($book->title, 0, 15) . '...' : $book->title }}</p>
                    <span class="book-author">Author : {{ $book->author }}</span>
                </a>
            </div>
            @endforeach
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
