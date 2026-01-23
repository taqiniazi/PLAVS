@extends('layouts.app')

@section('title', 'MyBookShelf - Forgot Password')

@push('styles')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 login-container">
        <div class="col-lg-6 left-panel">
            <div class="text-center">
                <div class="logo-text">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="img-fluid">
                </div>
                <h4 class="fw-bold mb-1">RESET PASSWORD</h4>
                <p class="text-muted mb-5">Enter your email to receive password reset link</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium small">Official Email ID</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Enter email address" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-custom mb-3">SEND RESET LINK</button>
                
                <div class="text-center">
                    <p class="text-muted small">Remember your password? <a href="{{ route('login') }}" class="text-dark fw-bold text-decoration-none">Sign in</a></p>
                </div>
            </form>
        </div>

        <div class="col-lg-6 right-panel d-none d-lg-block">
        </div>
    </div>
</div>
@endsection
