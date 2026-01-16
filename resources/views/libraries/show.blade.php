@extends('layouts.dashboard')

@section('title', $library->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')

@php
    $user = Auth::user();
    $isPublic = !($user->isSuperAdmin() || $user->isAdmin() || $user->isLibrarian() || $user->isOwner());
@endphp

<div class="container-fluid">
    <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">{{ $library->name }}</h2>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge {{ $library->type === 'public' ? 'bg-success' : 'bg-warning' }} badge-library-type">
                            <i class="fas fa-globe me-1"></i>
                            {{ ucfirst($library->type) }}
                        </span>
                        <span class="badge bg-primary text-light">
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
                    <a href="{{ route('libraries.edit', $library) }}" class="btn btn-info me-2">
                        <i class="fas fa-edit me-2"></i>Edit Library
                    </a>
                    @endcan
                    @can('create', App\Models\Room::class)
                    <a href="{{ route('libraries.rooms.create', $library) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Room
                    </a>
                    @endcan
                </div>
            </div>
    <!-- Library Header Section -->
    <div class="card">
       
        <div class="card-body">
            <!-- Library Details Section -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="library-info-card">
                        <h5 class="mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Library Details
                        </h5>
                        <div class="row">
                            <div class="col-md-4 info-item d-flex">
                                <div class="info-icon me-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Location</h6>
                                    <p class="mb-0 text-muted">{{ $library->location ?? 'Not specified' }}</p>
                                </div>
                            </div>

                            <div class="col-md-4 info-item d-flex">
                                <div class="info-icon me-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Owner</h6>
                                    <p class="mb-0 text-muted">{{ $library->owner ? $library->owner->name : 'System' }}</p>
                                </div>
                            </div>

                            @if($library->location)
                            <div class="col-md-4 info-item d-flex">
                                <div class="info-icon me-2">
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
                            <div class="info-item d-flex">
                                <div class="info-icon me-2">
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
                            <span class="badge bg-primary">{{ $library->books->count() }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total Rooms</span>
                            <span class="badge bg-info">{{ $library->rooms->count() }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Shelves</span>
                            <span class="badge bg-success">{{ $library->total_shelves }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    @if(!$isPublic)
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
                    <div class="table-card">
                        <div class="table-responsive">
                            <table id="roomsTable" class="table table-hover align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Total Books</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($library->rooms as $room)
                                    <tr>
                                        <td>{{ $room->name }}</td>
                                        <td>{{ $room->description ?? 'No description' }}</td>
                                        <td>{{ $room->total_books }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('libraries.rooms.show', [$library, $room]) }}"
                                                    class="btn btn-outline-primary btn-sm" title="View Room">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('update', $room)
                                                <a href="{{ route('rooms.edit', $room) }}"
                                                    class="btn btn-outline-warning btn-sm" title="Edit Room">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete', $room)
                                                <form method="POST" action="{{ route('rooms.destroy', $room) }}"
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
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
            @endif
    <!-- .row.mt-4>.card>.card-body -->
            <!-- Books Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>
                            <i class="fas fa-book me-2"></i>
                            Books in {{ $library->name }}
                        </h4>
                    </div>
                    <div class="table-card">
                        <div class="table-responsive">
                            <table id="booksTable" class="table table-hover align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>ISBN</th>
                                        <th>Room</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($library->books as $book)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="rounded me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                                <span class="fw-bold">{{ $book->title }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $book->author }}</td>
                                        <td>{{ $book->isbn }}</td>
                                        <td>{{ $book->shelf?->room?->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ strtolower($book->status) === 'available' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($book->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('view', $book)
                                            <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary" title="View Book">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invite Section -->
            @if(Auth::user()->can('update', $library) || (Auth::user()->isLibrarian() && Auth::user()->parent_owner_id === $library->owner_id))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-envelope me-2"></i>
                                Invite Members
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('invitations.store') }}" method="POST" class="row g-3 align-items-end">
                                @csrf
                                <input type="hidden" name="library_id" value="{{ $library->id }}">

                                <div class="col-md-5">
                                    <label for="invite_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="invite_email" name="email" required placeholder="Enter email to invite">
                                </div>

                                <div class="col-md-3">
                                    <label for="invite_role" class="form-label">Role</label>
                                    <select class="form-select" id="invite_role" name="role" required>
                                        <option value="public">Public</option>
                                        <option value="librarian">Librarian</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Send Invite
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
           
        </div>
    </div>



</div>

@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#booksTable').DataTable({
            "language": {
                "search": "Search books:"
            }
        });
        $('#roomsTable').DataTable({
            "language": {
                "search": "Search rooms:"
            }
        });
    });

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
