@extends('layouts.dashboard')

@section('title', 'Add New Shelf')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-bookmark me-2"></i>
                        Add New Shelf
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Create a new shelf to organize your books</p>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('shelves.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Shelf Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required-field">Shelf Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Enter shelf name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Library -->
                            <div class="col-md-6 mb-3">
                                <label for="library_id" class="form-label required-field">Library</label>
                                <select class="form-select @error('library_id') is-invalid @enderror" 
                                        id="library_id" 
                                        name="library_id" 
                                        required>
                                    <option value="">Select library</option>
                                    @foreach($libraries as $library)
                                        <option value="{{ $library->id }}" {{ old('library_id') == $library->id ? 'selected' : '' }}>
                                            {{ $library->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('library_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Room -->
                            <div class="col-md-6 mb-3">
                                <label for="room_id" class="form-label">Room</label>
                                <select class="form-select @error('room_id') is-invalid @enderror" 
                                        id="room_id" 
                                        name="room_id">
                                    <option value="">Select room</option>
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Shelf Code -->
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Shelf Code</label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}" 
                                       placeholder="e.g., A-01, B-02">
                                <div class="form-text">Optional: Add a unique code for easy identification</div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Describe what this shelf is for (optional)">{{ old('description') }}</textarea>
                                <div class="form-text">Optional: Add details about the shelf's purpose or contents</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="{{ route('shelves.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Create Shelf
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
    
    // Dynamic room loading based on selected library
    const librarySelect = document.getElementById('library_id');
    const roomSelect = document.getElementById('room_id');
    
    librarySelect.addEventListener('change', function() {
        const libraryId = this.value;
        
        // Clear current options except the default
        roomSelect.innerHTML = '<option value="">Select room</option>';
        
        if (libraryId) {
            // Fetch rooms for the selected library
            fetch(`/api/libraries/${libraryId}/rooms`)
                .then(response => response.json())
                .then(data => {
                    data.rooms.forEach(room => {
                        const option = document.createElement('option');
                        option.value = room.id;
                        option.textContent = room.name;
                        roomSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching rooms:', error);
                    roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
                });
        }
    });
    
    // Add some interactivity for better UX
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
        submitBtn.disabled = true;
    });
});
</script>
@endpush