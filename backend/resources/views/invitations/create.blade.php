@extends('layouts.dashboard')

@section('title', 'Send Invitation')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>Send Invitation
                    </h5>
                </div>
                <div class="card-body">
                    @if($libraries->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            You don't have any libraries to invite members to.
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                    @else
                        <form action="{{ route('invitations.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="library_id" class="form-label">Select Library</label>
                                <select class="form-select @error('library_id') is-invalid @enderror" id="library_id" name="library_id" required>
                                    <option value="">-- Select Library --</option>
                                    @foreach($libraries as $library)
                                        <option value="{{ $library->id }}" {{ old('library_id') == $library->id ? 'selected' : '' }}>
                                            {{ $library->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('library_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter recipient's email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                    <option value="librarian" {{ old('role') == 'librarian' ? 'selected' : '' }}>Librarian</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-envelope me-2"></i>Send Invitation
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
