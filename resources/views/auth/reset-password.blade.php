@extends('layouts.app')

@section('title', 'PLAVS - Reset Password')

@push('styles')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const groups = document.querySelectorAll('.login-container .input-group');
    groups.forEach(group => {
        const pwdInput = group.querySelector('input[type="password"]');
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
});
</script>
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
                <p class="text-muted mb-5">Create a new password for your account</p>
            </div>

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-3">
                    <label class="form-label fw-medium small">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
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

                <div class="mb-4">
                    <label class="form-label fw-medium small">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" class="form-control border-end-0" 
                               placeholder="************" required>
                        <span class="input-group-text bg-light border-start-0 text-muted"><i class="far fa-eye-slash"></i></span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-custom mb-3">RESET PASSWORD</button>
            </form>
        </div>

        <div class="col-lg-6 right-panel d-none d-lg-block">
        </div>
    </div>
</div>
@endsection
