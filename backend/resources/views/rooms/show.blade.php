@extends('layouts.dashboard')

@section('title', $room->name)

@section('content')

<div class="container-fluid">
    <!-- Room Header Section -->
    <div class="room-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">{{ $room->name }}</h2>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge bg-secondary">
                        <i class="fas fa-building me-1"></i>
                        {{ $room->library->name }}
                    </span>
                    <span class="text-light">
                        <i class="fas fa-shelf me-1"></i>
                        {{ $room->shelves_count }} Shelves
                    </span>
                </div>
                @if($room->description)
                    <p class="mb-0 opacity-75">{{ $room->description }}</p>
                @endif
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('libraries.show', $room->library) }}" class="btn btn-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Library
                </a>
                @can('update', $room)
                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-add-shelf">
                        <i class="fas fa-edit me-2"></i>Edit Room
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Room Details Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="room-info-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Room Details
                </h5>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Room Name</h6>
                        <p class="mb-0 text-muted">{{ $room->name }}</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Library</h6>
                        <p class="mb-0 text-muted">{{ $room->library->name }}</p>
                    </div>
                </div>
                
                @if($room->description)
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Description</h6>
                            <p class="mb-0 text-muted">{{ $room->description }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Room Statistics -->
            <div class="room-info-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistics
                </h5>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Shelves</span>
                    <span class="badge bg-info">{{ $room->shelves_count }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total Books</span>
                    <span class="badge bg-primary">{{ $room->shelves->sum('books_count') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Shelves Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>
                    <i class="fas fa-bookshelf me-2"></i>
                    Shelves
                </h4>
                <span class="text-muted">{{ $room->shelves_count }} shelves</span>
            </div>
            
            @if($room->shelves->count() > 0)
                <div class="row">
                    @foreach($room->shelves as $shelf)
                        <div class="col-md-6 col-lg-4">
                            <div class="shelf-card">
                                <div class="shelf-header">
                                    <div>
                                        <h5 class="mb-1">{{ $shelf->name }}</h5>
                                        @if($shelf->code)
                                            <p class="mb-0 text-muted small">Code: {{ $shelf->code }}</p>
                                        @endif
                                    </div>
                                    <div class="shelf-actions">
                                        <a href="{{ route('shelves.show', $shelf) }}" 
                                           class="btn btn-outline-primary btn-sm" title="View Shelf">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $shelf)
                                            <a href="{{ route('shelves.edit', $shelf) }}" 
                                               class="btn btn-outline-warning btn-sm" title="Edit Shelf">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $shelf)
                                            <form method="POST" action="{{ route('shelves.destroy', $shelf) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this shelf?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Shelf">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        <i class="fas fa-book me-1"></i>
                                        {{ $shelf->books_count }} Books
                                    </span>
                                    @if($shelf->description)
                                        <span class="text-muted small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ Str::limit($shelf->description, 30) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-bookshelf"></i>
                    <h5 class="text-muted">No shelves added yet</h5>
                    <p class="text-muted">Start by adding shelves to organize your books in this room.</p>
                    @can('create', App\Models\Shelf::class)
                        <a href="{{ route('shelves.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Your First Shelf
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add some interactivity for better UX
    const shelfCards = document.querySelectorAll('.shelf-card');
    
    shelfCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush