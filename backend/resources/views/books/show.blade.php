@extends('layouts.dashboard')

@section('title', $book->title . ' - Book Details')

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header">
            <h4 class="page-title">{{ $book->title }}</h4>
            <div class="header-actions">
                <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Library
                </a>
            </div>
        </div>
        
        <div class="row">
            {{-- Book Cover Image --}}
            <div class="col-md-4">
                <div class="book-detail-card">
                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/' . ($book->image ?? 'book1.png')) }}" 
                         alt="{{ $book->title }}" class="img-fluid book-cover">
                    
                    {{-- Rating Stars --}}
                    <div class="rating-section mt-3">
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= ($book->rating ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                        <small class="text-muted">{{ $book->rating ?? 0 }} out of 5</small>
                    </div>
                    
                    {{-- Wishlist Button --}}
                    <div class="mt-3">
                        <button class="btn btn-outline-danger w-100" id="wishlistBtn">
                            <i class="fas fa-heart"></i>
                            <span class="ms-2">Add to Wishlist</span>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Book Details --}}
            <div class="col-md-8">
                <div class="table-card">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Author</label>
                            <p class="fw-medium mb-0">{{ $book->author }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Publisher</label>
                            <p class="fw-medium mb-0">{{ $book->publisher ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">ISBN</label>
                            <p class="fw-medium mb-0">{{ $book->isbn ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Edition</label>
                            <p class="fw-medium mb-0">{{ $book->edition ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Category</label>
                            <p class="mb-0">
                                @if($book->category)
                                    <span class="badge" style="background-color: {{ $book->category->color ?? '#3498db' }};">
                                        {{ $book->category->name }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0">
                                <span class="badge {{ $book->status === 'Available' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $book->status }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    {{-- Shelf Location --}}
                    @if($book->shelf)
                    <div class="mb-3">
                        <label class="text-muted small">Shelf Location</label>
                        <p class="mb-0">
                            <span class="badge-shelf">
                                {{ $book->shelf->room->library->name }} > {{ $book->shelf->room->name }} > {{ $book->shelf->name }}
                            </span>
                        </p>
                    </div>
                    @endif
                    
                    <hr>
                    
                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="text-muted small">Description</label>
                        <p class="mb-0">{{ $book->description ?? 'No description available.' }}</p>
                    </div>
                    
                    {{-- Admin Actions --}}
                    @if(auth()->user()->hasAdminRole())
                    <hr>
                    <div class="d-flex gap-2">
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="fas fa-user-plus me-1"></i> Assign
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wishlistBtn = document.getElementById('wishlistBtn');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            // Wishlist functionality placeholder
            alert('Wishlist feature - implement backend endpoint');
        });
    }
});
</script>
@endpush
