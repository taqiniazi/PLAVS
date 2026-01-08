@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Add New Book')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-3">
            <h4 class="page-title">Add New Book</h4>
        </div>
        <div class="form-container">
            <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Book Cover Image</label>
                    <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" 
                           accept="image/*">
                    <small class="text-muted">Upload a cover image for the book (optional)</small>
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Book Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter Book Title" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Edition</label>
                        <input type="text" name="edition" class="form-control" placeholder="Enter edition">
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">ISBN Number</label>
                        <input type="text" name="isbn" class="form-control" placeholder="Enter ISBN">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Publisher Name</label>
                        <input type="text" name="publisher" class="form-control" placeholder="Enter publisher name">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Publish Date</label>
                        <input type="date" name="publish_date" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Author</label>
                        <select name="author" class="form-select" required>
                            <option selected disabled>Select</option>
                            @foreach($authors as $author)
                            <option value="{{ $author }}">{{ $author }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Shelf Location</label>
                        <select name="shelf" class="form-select" required>
                            <option selected disabled>Select</option>
                            @foreach($shelves as $shelf)
                            <option value="{{ $shelf }}">{{ $shelf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Owner</label>
                    <select name="owner" class="form-select" required>
                        <option selected disabled>Select</option>
                        @foreach($owners as $owner)
                        <option value="{{ $owner }}">{{ $owner }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Brief description of the book"></textarea>
                </div>

                <div class="mt-4 ms-auto">
                    <button type="submit" class="btn btn-submit">Add Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection