@extends('layouts.dashboard')

@section('title', 'PLAVS  - Change Password')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-6 mx-auto">
        <div class="page-header mb-3">
            <h4 class="page-title">Change Password</h4>
        </div>
        <div class="form-container">
            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" 
                           placeholder="Enter your current password" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Enter new password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           placeholder="Confirm new password" required>
                </div>

                <div class="alert alert-info">
                    <small>
                        <strong>Password Requirements:</strong><br>
                        • At least 8 characters long<br>
                        • Mix of uppercase and lowercase letters<br>
                        • At least one number<br>
                        • At least one special character
                    </small>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-submit">Update Password</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection