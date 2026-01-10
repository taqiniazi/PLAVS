@extends('layouts.dashboard')

@section('title', 'My Wishlist')

@push('styles')
<style>
    .wishlist-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .wishlist-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .book-cover {
        width: 80px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .book-info h5 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: #333;
    }
    
    .book-info p {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 15px;
    }
    
    .btn-remove {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-remove:hover {
        background: #c82333;
        transform: translateY(-1px);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
        border: 1px dashed #dee2e6;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .rating-stars {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    .rating-count {
        color: #6c757d;
        font-size: 0.85rem;
        margin-left: 5px;
    }
</style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h4 class="page-title">
                    <i class="fas fa-heart me-2"></i>
                    My Wishlist
                </h4>
                <p class="text-muted mb-0">Books you've saved for later</p>
            </div>

            @if($wishlistBooks->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-heart-broken"></i>
                    <h5 class="text-muted">Your wishlist is empty</h5>
                    <p class="text-muted">Start adding books to your wishlist by visiting the books section.</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary">
                        <i class="fas fa-book me-2"></i> Browse Books
                    </a>
                </div>
            @else
                <div class="row">
                    @foreach($wishlistBooks as $wishlistItem)
                        @php $book = $wishlistItem->book; @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="wishlist-card">
                                <div class="row g-0">
                                    <div class="col-4 d-flex align-items-center justify-content-center p-3">
                                        @if($book->cover_image)
                                            <img src="{{ $book->cover_url }}"
                                                 alt="{{ $book->title }}"
                                                 class="book-cover">
                                        @else
                                            <div class="book-cover d-flex align-items-center justify-content-center bg-light text-muted">
                                                <i class="fas fa-book fa-2x"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body">
                                            <div class="book-info">
                                                <h5 class="mb-1">{{ Str::limit($book->title, 40) }}</h5>
                                                <p class="mb-1">
                                                    <strong>Author:</strong> {{ Str::limit($book->author, 30) }}
                                                </p>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-secondary status-badge">
                                                        {{ $book->status }}
                                                    </span>
                                                    @if($book->average_rating > 0)
                                                        <div class="rating-stars ms-2">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= round($book->average_rating))
                                                                    <i class="fas fa-star"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <span class="rating-count">
                                                            ({{ $book->rating_count }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('books.details', $book) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i> View Details
                                                </a>
                                                <form method="POST" action="{{ route('wishlist.toggle', $book) }}" 
                                                      style="display: inline;" 
                                                      class="wishlist-toggle-form">
                                                    @csrf
                                                    <button type="submit" class="btn-remove btn-sm">
                                                        <i class="fas fa-trash me-1"></i> Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle wishlist toggle
    const wishlistForms = document.querySelectorAll('.wishlist-toggle-form');
    
    wishlistForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Removing...';
            button.disabled = true;
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the card from the DOM
                    const card = form.closest('.wishlist-card').parentElement;
                    card.remove();
                    
                    // Show success message
                    showToast(data.message, 'success');
                    
                    // If wishlist becomes empty, show empty state
                    if (document.querySelectorAll('.wishlist-card').length === 0) {
                        showEmptyState();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });
    });
    
    function showToast(message, type = 'success') {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    function showEmptyState() {
        const container = document.querySelector('.col-12');
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-heart-broken"></i>
                <h5 class="text-muted">Your wishlist is empty</h5>
                <p class="text-muted">Start adding books to your wishlist by visiting the books section.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">
                    <i class="fas fa-book me-2"></i> Browse Books
                </a>
            </div>
        `;
    }
});
</script>
@endpush