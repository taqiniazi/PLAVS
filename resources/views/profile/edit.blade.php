@extends('layouts.dashboard')

@section('title', 'PLAVS - Update Profile')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-8 mx-auto">
        <div class="page-header mb-3">
            <h4 class="page-title">Update Profile</h4>
        </div>
        <div class="form-container">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <img id="profile-avatar-preview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/user.png') }}" 
                             alt="Profile" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                        <label for="avatar" class="change-avatar" 
                               style="width: 35px; height: 35px; cursor: pointer;">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                    </div>
                    @error('avatar')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           placeholder="Enter your full name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Enter your email address" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                           placeholder="Enter your phone number" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- <div class="mb-4">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly>
                    <small class="text-muted">Role cannot be changed. Contact administrator if needed.</small>
                </div> -->

                <div class="col-md-12 justify-content-center align-items-center mt-4 d-flex gap-2">
                    <div class="mt-4 d-flex align-items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                    @php
                        $hasPendingOwnerRequest = \App\Models\OwnerRequest::where('user_id', $user->id)->where('status', 'pending')->exists();
                    @endphp
                    @if($user->isPublic())
                        @if(!$hasPendingOwnerRequest)
                            <form action="{{ route('permissions.request-owner') }}" method="POST" class="ms-3">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-user-shield me-2"></i>Request Owner Role
                                </button>
                            </form>
                            <small class="text-muted d-block mt-2">Your request will notify administrators. You will be upgraded to Owner once approved.</small>
                        @else
                            <div class="alert alert-info ms-3 mb-0" role="alert">
                                <i class="fas fa-hourglass-half"></i> Your request for Owner role is pending approval.
                            </div>
                        @endif
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('profile-avatar-preview');
            if (previewImg) {
                previewImg.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
