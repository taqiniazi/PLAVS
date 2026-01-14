@extends('layouts.dashboard')

@section('title', 'Edit Library')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Library
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Update library information</p>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('libraries.update', $library) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Library Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required-field">Library Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $library->name) }}" 
                                       placeholder="Enter library name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Library Type -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label required-field">Library Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">Select library type</option>
                                    <option value="public" {{ old('type', $library->type) == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ old('type', $library->type) == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Location -->
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label required-field">Location</label>
                                <input type="text" 
                                       class="form-control @error('location') is-invalid @enderror" 
                                       id="location" 
                                       name="location" 
                                       value="{{ old('location', $library->location) }}" 
                                       placeholder="Enter library location"
                                       required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="map_link" class="form-label">Map Link</label>
                                <input type="url" 
                                       class="form-control @error('map_link') is-invalid @enderror" 
                                       id="map_link" 
                                       name="map_link" 
                                       value="{{ old('map_link', $library->map_link) }}" 
                                       placeholder="https://maps.google.com/...">
                                <!-- <div class="form-text">Optional: Add a Google Maps link for easy navigation</div> -->
                                @error('map_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="image" class="form-label">Library Logo</label>
                                @if($library->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $library->image) }}" alt="Library Logo" class="img-fluid rounded" style="max-height: 120px;">
                                    </div>
                                @endif
                                <input type="file"
                                       class="form-control @error('image') is-invalid @enderror"
                                       id="image"
                                       name="image"
                                       accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Owner Information (Read-only) -->
                            <div class="col-md-12 mb-3">
                                <label for="owner_info" class="form-label">Library Owner</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Name:</strong> {{ $library->owner ? $library->owner->name : 'System' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Email:</strong> {{ $library->owner ? $library->owner->email : 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Role:</strong> {{ $library->owner ? $library->owner->getRoleDisplayName() : 'System' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Library Type:</strong> {{ ucfirst($library->type) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text text-muted">Owner information is managed through user profiles</div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="{{ route('libraries.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>Update Library
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
