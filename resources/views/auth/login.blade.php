@extends('layouts.app')

@section('title', 'PLAVS  - Login')

@push('styles')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const group = document.querySelector('.login-container .input-group');
    if (!group) return;
    const pwdInput = group.querySelector('input[name="password"]');
    const toggle = group.querySelector('.input-group-text');
    const icon = group.querySelector('.input-group-text i');
    if (pwdInput && toggle) {
        toggle.style.cursor = 'pointer';
        toggle.setAttribute('title', 'Show/Hide Password');
        toggle.addEventListener('click', function () {
            const isHidden = pwdInput.type === 'password';
            pwdInput.type = isHidden ? 'text' : 'password';
            if (icon) {
                icon.classList.toggle('fa-eye', isHidden);
                icon.classList.toggle('fa-eye-slash', !isHidden);
            }
        });
    }
});
</script>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 login-container" style="overflow-y:auto">
        <div class="col-lg-6 left-panel">
            <div class="text-center">
                <div class="logo-text">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="img-fluid">
                </div>
                <h4 class="fw-bold mb-1">SIGN IN</h4>
                <p class="text-muted mb-5">Welcome! Please enter your details</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium small">Official Email ID</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Enter email address" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-medium small">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control border-end-0 @error('password') is-invalid @enderror" 
                               placeholder="************" required>
                        <span class="input-group-text bg-light border-start-0 text-muted"><i class="far fa-eye-slash"></i></span>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-muted" for="remember">
                            Remember me
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-custom mb-3">SIGN IN</button>
                
                <div class="text-center">
                    <p class="text-muted small">Don't have an account? <a href="{{ route('register') }}" class="text-dark fw-bold text-decoration-none">Sign up</a></p>
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot Password?</a>
                </div>
            </form>
        </div>

        <div class="col-lg-6 right-panel d-none d-lg-block">
        </div>
    </div>
</div>
@endsection