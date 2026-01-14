@extends('layouts.dashboard')

@section('title', 'Edit Room: ' . $room->name)

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Room: {{ $room->name }}
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Update room information</p>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            @if(isset($libraries) && $libraries->count() > 0)
                                <div class="col-md-12 mb-3">
                                    <label for="library_id" class="form-label required-field">Library</label>
                                    @if($libraries->count() > 1)
                                        <select class="form-select @error('library_id') is-invalid @enderror"
                                                id="library_id"
                                                name="library_id"
                                                required>
                                            <option value="">Select library</option>
                                            @php($currentLibraryId = old('library_id', $room->library_id))
                                            @foreach($libraries as $library)
                                                <option value="{{ $library->id }}" {{ (string) $currentLibraryId === (string) $library->id ? 'selected' : '' }}>
                                                    {{ $library->name }}{{ $library->location ? ' - ' . $library->location : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        @php($singleLibrary = $libraries->first())
                                        <input type="hidden" id="library_id" name="library_id" value="{{ $singleLibrary->id }}">
                                        <div class="library-info p-2 border rounded">
                                            <h6 class="mb-1"><i class="fas fa-building me-2"></i>Library: {{ $singleLibrary->name }}</h6>
                                            <p class="mb-0 small"><i class="fas fa-map-marker-alt me-1"></i>{{ $singleLibrary->location ?? 'No location specified' }}</p>
                                        </div>
                                    @endif
                                    @error('library_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            
                            <!-- Room Name -->
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label required-field">Room Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $room->name) }}" 
                                       placeholder="Enter room name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Describe this room (optional)">{{ old('description', $room->description) }}</textarea>
                                <div class="form-text">Optional: Add details about the room's purpose or features</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="{{ route('libraries.show', $room->library_id) }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>Update Room
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on the first input field
    document.getElementById('name').focus();
    
    // Add some interactivity for better UX
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        submitBtn.disabled = true;
    });
});
</script>
@endpush
