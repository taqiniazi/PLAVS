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
                    <img src="{{ $book->cover_url }}"
                         alt="{{ $book->title }}" class="img-fluid book-cover">
                    
                    {{-- Rating Section --}}
                    <div class="rating-section mt-3">
                        <label class="text-muted small d-block mb-2">Rate this book</label>
                        <form id="ratingForm">
                            <input type="hidden" name="rating" id="ratingValue" value="0">
                            <div class="rating-stars mb-2" id="ratingStars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="far fa-star fa-lg me-1 rate-star" data-value="{{ $i }}"></i>
                                @endfor
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary" id="averageRating">
                                     {{ round($book->average_rating, 1) }}/5
                                 </span>
                                 @php $reviewCount = isset($ratings) ? $ratings->count() : ($book->rating_count ?? 0); @endphp
                                 <small class="text-muted ms-2" id="ratingCount">
                                     ({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})
                                 </small>
                            </div>
                            <div class="mt-2">
                                <textarea class="form-control" id="reviewText" name="review" rows="3"
                                          placeholder="Share your thoughts about this book...">{{ $userRating->review ?? '' }}</textarea>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm mt-2" id="saveRatingBtn" style="display: none;">
                                <i class="fas fa-save me-1"></i> Save Rating
                            </button>
                        </form>
                    </div>
                    
                    {{-- Wishlist Button --}}
                    <div class="mt-3">
                        <button class="btn btn-primary w-100" id="wishlistBtn">
                            <i class="fas fa-heart-o me-1"></i>
                            <span id="wishlistText">Add to Wishlist</span>
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
                                    <span class="badge" style="background-color: #3498db;">
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
                    {{-- Ratings Area --}}
                    <label class="text-muted small">Ratings</label>
                    <div class="card mb-3">
                        <div class="card-body bg-light">
                            @if(isset($ratings) && $ratings->isEmpty())
                                <p class="text-muted mb-0">No reviews yet. Be the first to review.</p>
                            @else
                                <ul class="list-unstyled mb-0">
                                    @foreach($ratings as $rating)
                                        @php $user = $rating->user; @endphp
                                        <li class="mb-3">
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <div class="avatar-small rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center">
                                                        <strong>{{ $user->name ?? 'Unknown User' }}</strong>
                                                        <small class="text-muted ms-2">{{ optional($rating->created_at)->diffForHumans() }}</small>
                                                    </div>
                                                    <div class="mt-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="{{ $i <= (int)($rating->rating ?? 0) ? 'fas fa-star text-warning' : 'far fa-star' }} me-1"></i>
                                                        @endfor
                                                    </div>
                                                    @if(!empty($rating->review))
                                                        <p class="mb-0 mt-2">{{ $rating->review }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Admin Actions --}}
                    @if(auth()->user()->can('update', $book) || auth()->user()->can('assign', $book))
                    <hr>
                    <div class="d-flex gap-2">
                        @can('update', $book)
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @endcan
                        
                        @can('assign', $book)
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="fas fa-user-plus me-1"></i> Assign
                        </button>
                        @endcan
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
    const bookId = {{ $book->id }};
    const userId = {{ auth()->id() }};
    
    // Check if user has already rated this book
    let userRating = null;
    
    // Initialize wishlist button state
    checkWishlistStatus();
    
    // Initialize rating stars
    initRatingStars();
    
    // Wishlist Button Functionality
    const wishlistBtn = document.getElementById('wishlistBtn');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            toggleWishlist();
        });
    }
    
    // Save Rating Button Functionality
    const saveRatingBtn = document.getElementById('saveRatingBtn');
    if (saveRatingBtn) {
        saveRatingBtn.addEventListener('click', function() {
            saveRating();
        });
    }
    
    function checkWishlistStatus() {
        // Check if book is in wishlist via AJAX
        fetch(`/api/wishlist/check/${bookId}`)
            .then(response => response.json())
            .then(data => {
                updateWishlistButton(data.is_in_wishlist);
            })
            .catch(error => {
                console.error('Error checking wishlist:', error);
            });
    }
    
    function toggleWishlist() {
        const wishlistBtn = document.getElementById('wishlistBtn');
        const wishlistText = document.getElementById('wishlistText');
        const icon = wishlistBtn.querySelector('i');
        
        wishlistBtn.disabled = true;
        wishlistText.textContent = 'Processing...';
        
        fetch(`/wishlist/toggle/${bookId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWishlistButton(data.is_in_wishlist);
                showToast(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Error toggling wishlist:', error);
            showToast('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            wishlistBtn.disabled = false;
            wishlistText.textContent = document.getElementById('wishlistText').textContent;
        });
    }
    
    function updateWishlistButton(isInWishlist) {
        const wishlistBtn = document.getElementById('wishlistBtn');
        const wishlistText = document.getElementById('wishlistText');
        const icon = wishlistBtn.querySelector('i');
        
        if (isInWishlist) {
            wishlistBtn.className = 'btn btn-danger w-100';
            wishlistText.textContent = 'Remove from Wishlist';
            icon.className = 'fas fa-heart me-1';
        } else {
            wishlistBtn.className = 'btn btn-primary w-100';
            wishlistText.textContent = 'Add to Wishlist';
            icon.className = 'fas fa-heart-o me-1';
        }
    }
    
    function initRatingStars() {
        const stars = document.querySelectorAll('#ratingStars i.rate-star');
        const reviewText = document.getElementById('reviewText');
        const saveRatingBtn = document.getElementById('saveRatingBtn');
        const ratingValue = document.getElementById('ratingValue');
        
        // Load user's existing rating if any
        loadUserRating();
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-value'));
                ratingValue.value = rating;
                updateStars(rating);
                saveRatingBtn.style.display = 'inline-block';
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-value'));
                highlightStars(rating);
            });
            
            star.addEventListener('mouseleave', function() {
                resetStars();
            });
        });
        
        reviewText.addEventListener('input', function() {
            saveRatingBtn.style.display = 'inline-block';
        });
    }
    
    function loadUserRating() {
        // This would typically be passed from the backend
        // For now, we'll fetch it via AJAX
        fetch(`/api/books/${bookId}/user-rating`)
            .then(response => response.json())
            .then(data => {
                if (data.rating) {
                    userRating = data.rating;
                    updateStars(userRating.rating);
                    document.getElementById('reviewText').value = userRating.review || '';
                }
            })
            .catch(error => {
                console.error('Error loading user rating:', error);
            });
    }
    
    function updateStars(rating) {
        const stars = document.querySelectorAll('#ratingStars i.rate-star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.className = 'fas fa-star fa-lg me-1 text-warning rate-star';
            } else {
                star.className = 'far fa-star fa-lg me-1 rate-star';
            }
        });
    }
    
    function highlightStars(rating) {
        const stars = document.querySelectorAll('#ratingStars i.rate-star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.style.color = '#ffc107';
            } else {
                star.style.color = '';
            }
        });
    }
    
    function resetStars() {
        const stars = document.querySelectorAll('#ratingStars i.rate-star');
        stars.forEach((star, index) => {
            if (userRating && index < userRating.rating) {
                star.className = 'fas fa-star fa-lg me-1 text-warning rate-star';
            } else {
                star.className = 'far fa-star fa-lg me-1 rate-star';
            }
        });
    }
    
    function saveRating() {
        const rating = parseInt(document.getElementById('ratingValue').value);
        const review = document.getElementById('reviewText').value;
        
        if (rating === 0) {
            showToast('Please select a rating first.', 'warning');
            return;
        }
        
        const saveRatingBtn = document.getElementById('saveRatingBtn');
        saveRatingBtn.disabled = true;
        saveRatingBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
        
        fetch(`/books/${bookId}/rating`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                rating: rating,
                review: review
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userRating = { rating: data.rating, review: data.review };
                updateAverageRating(data.average_rating, data.rating_count);
                saveRatingBtn.style.display = 'none';
                showToast('Rating saved successfully!', 'success');
            }
        })
        .catch(error => {
            console.error('Error saving rating:', error);
            showToast('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            saveRatingBtn.disabled = false;
            saveRatingBtn.innerHTML = '<i class="fas fa-save me-1"></i> Save Rating';
        });
    }
    
    function updateAverageRating(averageRating, ratingCount) {
        document.getElementById('averageRating').textContent = `${averageRating}/5`;
        document.getElementById('ratingCount').textContent = `(${ratingCount} ${ratingCount === 1 ? 'review' : 'reviews'})`;
    }
    
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} alert-dismissible fade show position-fixed`;
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
});
</script>
@endpush
