@extends('layouts.dashboard')

@section('title', 'Add New Library')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="form-card">
                <div class="form-header">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Add New Library
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Create a new library to organize your book collection</p>
                </div>
                
                <div class="form-body">
                    <form action="{{ route('libraries.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Library Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required-field">Library Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
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
                                    <option value="public" {{ old('type') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>Private</option>
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
                                       value="{{ old('location') }}" 
                                       placeholder="Enter library location"
                                       required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Map Link -->
                            <div class="col-md-6 mb-3">
                                <label for="map_link" class="form-label">Map Link</label>
                                <input type="url" 
                                       class="form-control @error('map_link') is-invalid @enderror" 
                                       id="map_link" 
                                       name="map_link" 
                                       value="{{ old('map_link') }}" 
                                       placeholder="https://maps.google.com/...">
                                <div class="form-text">Optional: Add a Google Maps link for easy navigation</div>
                                @error('map_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Owner Name -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_name" class="form-label required-field">Owner Name</label>
                                <input type="text"
                                       class="form-control @error('owner_name') is-invalid @enderror"
                                       id="owner_name"
                                       name="owner_name"
                                       value="{{ old('owner_name') }}"
                                       placeholder="Enter owner's full name"
                                       required>
                                @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Owner Email -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_email" class="form-label required-field">Owner Email</label>
                                <input type="email"
                                       class="form-control @error('owner_email') is-invalid @enderror"
                                       id="owner_email"
                                       name="owner_email"
                                       value="{{ old('owner_email') }}"
                                       placeholder="owner@example.com"
                                       required>
                                @error('owner_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Owner Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_phone" class="form-label required-field">Owner Phone</label>
                                <input type="tel"
                                       class="form-control @error('owner_phone') is-invalid @enderror"
                                       id="owner_phone"
                                       name="owner_phone"
                                       value="{{ old('owner_phone') }}"
                                       placeholder="+1234567890"
                                       required>
                                <div class="form-text">Please include country code (e.g., +1234567890)</div>
                                @error('owner_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                    <i class="fas fa-plus me-2"></i>Create Library
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
        submitBtn.disabled = true;
    });
});
</script>
@endpush