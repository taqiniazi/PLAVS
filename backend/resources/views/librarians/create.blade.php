@extends('layouts.dashboard')

@section('title', 'Add Librarian')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-8 mx-auto">
        <div class="card">
        <div class="card-header mb-3  d-flex justify-content-between align-items-center">
            <h4 class="page-title text-white mb-0">Add Librarian</h4>
            <a href="{{ route('libraries.index') }}" class="btn btn-outline-warning">Back to Libraries</a>
        </div>
        <div class="card-body">
        <div class="form-container p-0">
            <form action="{{ route('librarians.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone (optional)</label>
                    <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary">Create Librarian</button>
                </div>
            </form>
        </div>
        </div>
        </div>
    </div>
</div>
@endsection