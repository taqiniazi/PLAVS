@extends('layouts.dashboard')

@section('title', 'Add New Library')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Add New Library
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Create a new library to organize your book collection</p>
                </div>
                
                <div class="card-body ">
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
                                <!-- <div class="form-text">Optional: Add a Google Maps link for easy navigation</div> -->
                                @error('map_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @php($isOwner = Auth::user()->isOwner())

                            <!-- Owner Name -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_name" class="form-label">Owner Name</label>
                                <input type="text"
                                       class="form-control @error('owner_name') is-invalid @enderror"
                                       id="owner_name"
                                       name="owner_name"
                                       value="{{ $isOwner ? Auth::user()->name : old('owner_name') }}"
                                       placeholder="Enter owner's full name"
                                       {{ $isOwner ? 'readonly' : '' }}>
                                @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($isOwner)
                                    <div class="form-text">You are logged in as the owner. The library will be created under your account.</div>
                                @endif
                            </div>
                            
                            @if(!$isOwner)
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
                                <div class="form-text">If this email already exists, the new library will be attached to that owner. Otherwise, fill in name, phone, and password to create a new owner.</div>
                                @error('owner_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                            
                            <!-- Owner Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_phone" class="form-label">Owner Phone</label>
                                <input type="tel"
                                       class="form-control @error('owner_phone') is-invalid @enderror"
                                       id="owner_phone"
                                       name="owner_phone"
                                       value="{{ $isOwner ? Auth::user()->phone : old('owner_phone') }}"
                                       placeholder="+1234567890"
                                       {{ $isOwner ? 'readonly' : '' }}>
                                <div class="form-text">Please include country code (e.g., +1234567890)</div>
                                @error('owner_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @if(!$isOwner)
                            <!-- Owner Password -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_password" class="form-label">Owner Password</label>
                                <input type="password"
                                       class="form-control @error('owner_password') is-invalid @enderror"
                                       id="owner_password"
                                       name="owner_password"
                                       placeholder="Create a secure password">
                                <div class="form-text">Password must be at least 8 characters. Required only if you're creating a new owner.</div>
                                @error('owner_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password Confirmation -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password"
                                       class="form-control @error('owner_password_confirmation') is-invalid @enderror"
                                       id="owner_password_confirmation"
                                       name="owner_password_confirmation"
                                       placeholder="Confirm your password">
                                @error('owner_password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                            
                            <!-- Contact Information Section -->
                            <div class="col-12 mt-4">
                                <h6 class="mb-3">
                                    <i class="fas fa-address-book me-2"></i>
                                    Library Contact Information
                                </h6>
                                <p class="text-muted small mb-4">
                                    Public contact information for the library (different from owner's personal details)
                                </p>
                            </div>
                            
                            <!-- Contact Email -->
                            <div class="col-md-6 mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email"
                                       class="form-control @error('contact_email') is-invalid @enderror"
                                       id="contact_email"
                                       name="contact_email"
                                       value="{{ old('contact_email') }}"
                                       placeholder="library@example.com">
                                <div class="form-text">Public email for library inquiries</div>
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Contact Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="contact_phone" class="form-label">Contact Phone</label>
                                <input type="tel"
                                       class="form-control @error('contact_phone') is-invalid @enderror"
                                       id="contact_phone"
                                       name="contact_phone"
                                       value="{{ old('contact_phone') }}"
                                       placeholder="+1234567890">
                                <div class="form-text">Public phone number for library</div>
                                @error('contact_phone')
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
                                <button type="submit" class="btn btn-success w-100">
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