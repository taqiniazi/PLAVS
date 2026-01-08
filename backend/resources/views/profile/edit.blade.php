@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Update Profile')

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
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/user.png') }}" 
                             alt="Profile" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                        <label for="avatar" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle" 
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
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly>
                    <small class="text-muted">Role cannot be changed. Contact administrator if needed.</small>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-submit">Update Profile</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-light">Cancel</a>
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
            document.querySelector('img').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection