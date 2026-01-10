@extends('layouts.dashboard')

@section('title', $library->name)

@section('content')

<div class="container-fluid">
    <!-- Library Header Section -->
    <div class="card">
            <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">{{ $library->name }}</h2>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge {{ $library->type === 'public' ? 'bg-success' : 'bg-warning' }} badge-library-type">
                            <i class="fas fa-globe me-1"></i>
                            {{ ucfirst($library->type) }}
                        </span>
                        <span class="text-light">
                            <i class="fas fa-book me-1"></i>
                            {{ $library->books_count }} Books
                        </span>
                    </div>
                    @if($library->description)
                        <p class="mb-0 opacity-75">{{ $library->description }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-md-end">
                    @can('update', $library)
                        <a href="{{ route('libraries.edit', $library) }}" class="btn btn-light me-2">
                            <i class="fas fa-edit me-2"></i>Edit Library
                        </a>
                    @endcan
                    @can('create', App\Models\Room::class)
                        <a href="{{ route('libraries.rooms.create', $library) }}" class="btn btn-add-room">
                            <i class="fas fa-plus me-2"></i>Add Room
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body">
<!-- Library Details Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="library-info-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Library Details
                </h5>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Location</h6>
                        <p class="mb-0 text-muted">{{ $library->location ?? 'Not specified' }}</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Owner</h6>
                        <p class="mb-0 text-muted">{{ $library->owner ? $library->owner->name : 'System' }}</p>
                    </div>
                </div>
                
                @if($library->location)
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Map</h6>
                            <a href="https://maps.google.com/?q={{ urlencode($library->location) }}"
                               target="_blank" class="text-decoration-none">
                                <i class="fas fa-map-marker-alt me-2"></i>View on Google Maps
                            </a>
                        </div>
                    </div>
                @endif
                
                @if($library->description)
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Description</h6>
                            <p class="mb-0 text-muted">{{ $library->description }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Library Statistics -->
            <div class="library-info-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistics
                </h5>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Books</span>
                    <span class="badge bg-primary">{{ $library->books_count }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Rooms</span>
                    <span class="badge bg-info">{{ $library->rooms->count() }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total Shelves</span>
                    <span class="badge bg-success">{{ $library->rooms->sum('shelves_count') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>
                    <i class="fas fa-door-open me-2"></i>
                    Rooms
                </h4>
                <span class="text-muted">{{ $library->rooms->count() }} rooms</span>
            </div>
            
            @if($library->rooms->count() > 0)
                <div class="row">
                    @foreach($library->rooms as $room)
                        <div class="col-md-6 col-lg-4">
                            <div class="room-card">
                                <div class="room-header">
                                    <div>
                                        <h5 class="mb-1">{{ $room->name }}</h5>
                                        <p class="mb-0 text-muted small">{{ $room->description ?? 'No description' }}</p>
                                    </div>
                                    <div class="room-actions">
                                        <a href="{{ route('libraries.rooms.show', [$library, $room]) }}" 
                                           class="btn btn-outline-primary btn-sm" title="View Room">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $room)
                                            <a href="{{ route('libraries.rooms.edit', [$library, $room]) }}" 
                                               class="btn btn-outline-warning btn-sm" title="Edit Room">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $room)
                                            <form method="POST" action="{{ route('libraries.rooms.destroy', [$library, $room]) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this room?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Room">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        <i class="fas fa-bookshelf me-1"></i>
                                        {{ $room->shelves_count }} Shelves
                                    </span>
                                    <span class="text-muted small">
                                        <i class="fas fa-book me-1"></i>
                                        {{ $room->shelves->sum('books_count') }} Books
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-door-closed"></i>
                    <h5 class="text-muted">No rooms added yet</h5>
                    <p class="text-muted">Start by adding your first room to organize your book collection.</p>
                    @can('create', App\Models\Room::class)
                        <a href="{{ route('libraries.rooms.create', $library) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Your First Room
                        </a>
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
    // Add some interactivity for better UX
    const roomCards = document.querySelectorAll('.room-card');
    
    roomCards.forEach(card => {
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