@extends('layouts.dashboard')

@section('title', $room->name)

@section('content')

<div class="container-fluid px-0">
    <div class="row mb-4 align-items-center">
                <div class="col-md-7">
                    <h2 class="mb-2">{{ $room->name }}</h2>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-success text-light">
                            <i class="fas fa-building me-1"></i>
                            {{ $room->library->name }}
                        </span>
                        <span class="badge bg-primary text-light">
                            <i class="fas fa-shelf me-1"></i>
                            {{ $room->total_shelves }} Shelves
                        </span>
                    </div>
                    @if($room->description)
                        <p class="mb-0 opacity-75">{{ $room->description }}</p>
                    @endif
                </div>
                <div class="col-md-5 text-md-end">
                    <a href="{{ route('libraries.show', $room->library) }}" class="btn btn-outline-dark me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Library
                    </a>
                    @can('update', $room)
                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Room
                        </a>
                    @endcan
                </div>
            </div>
    <!-- Room Header Section -->
     <div class="card">
       

        <div class="card-body">
 <!-- Room Details Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="room-info-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Room Details
                </h5>
                <div class="d-flex">
                    <div class="info-item d-flex">
                    <div class="info-icon me-2">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Room Name</h6>
                        <p class="mb-0 text-muted">{{ $room->name }}</p>
                    </div>
                </div>
                
                <div class="info-item d-flex">
                    <div class="info-icon me-2">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Library Name</h6>
                        <p class="mb-0 text-muted">{{ $room->library->name }}</p>
                    </div>
                </div>
                
                @if($room->description)
                    <div class="info-item d-flex">
                        <div class="info-icon  me-2">
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
                    <span class="badge bg-info">{{ $room->total_shelves }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total Books</span>
                    <span class="badge bg-primary">{{ $room->total_books }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Shelves Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    <i class="fas fa-building me-2"></i>
                    Shelves
                </h5>
                <span class="text-muted"><strong>Total Shelves </strong> {{ $room->total_shelves }}</span>
            </div>
            
            @if($room->shelves->count() > 0)
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Total Books</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($room->shelves as $shelf)
                                <tr>
                                    <td>{{ $shelf->name }}</td>
                                    <td>{{ $shelf->code ?? 'N/A' }}</td>
                                    <td>{{ $shelf->total_books }}</td>
                                    <td>{{ $shelf->description ? Str::limit($shelf->description, 50) : 'No description' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
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
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
     </div>
    
   
</div>
 
@endsection
