@extends('layouts.app')

@section('title', 'PLAVS  - Register')

@push('styles')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid p-0" style="overflow-y:auto;">
    <div class="row g-0 login-container">
        <div class="col-lg-6 left-panel">
            <div class="text-center">
                <div class="logo-text mx-auto">
                    <img src="{{ asset('images/login_logo.png') }}" alt="" class="img-fluid">
                </div>
                <h4 class="fw-bold mb-1">SIGN UP</h4>
                <p class="text-muted mb-5">Create your account to get started</p>
            </div>

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium small">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           placeholder="Enter your full name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium small">Official Email ID</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Enter email address" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium small">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="************" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium small">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           placeholder="************" required>
                </div>

                <button type="submit" class="btn btn-custom mb-3">SIGN UP</button>
                
                <div class="text-center">
                    <p class="text-muted small">Already have an account? <a href="{{ route('login') }}" class="text-dark fw-bold text-decoration-none">Sign in</a></p>
                </div>
            </form>
        </div>

        <div class="col-lg-6 right-panel d-none d-lg-block">
        </div>
    </div>
</div>
@endsection