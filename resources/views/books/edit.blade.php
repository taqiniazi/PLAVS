@extends('layouts.dashboard')

@section('title', 'PLAVS - Edit Book')

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-3">
            <h4 class="page-title">Edit Book</h4>
        </div>
        <div class="form-container">
            <form action="{{ route('books.update', $book) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="form-label">Book Cover Image</label>
                    <div class="mb-2">
                        <img id="edit-image-preview" src="{{ $book->cover_url }}" alt="Cover" style="max-width:120px;" />
                    </div>
                    <input type="file" name="cover_image" class="form-control mb-2" accept="image/*">
                    <input type="hidden" name="scanned_image_url" id="edit_scanned_image_url" value="">
                    <small class="text-muted">Upload a new cover image to replace the existing one (optional)</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Book Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                           placeholder="Enter Book Title" value="{{ old('title', $book->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Edition</label>
                        <input type="text" name="edition" class="form-control @error('edition') is-invalid @enderror" 
                               placeholder="Enter edition" value="{{ old('edition', $book->edition) }}">
                        @error('edition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">ISBN Number</label>
                        <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" 
                               placeholder="Enter ISBN" value="{{ old('isbn', $book->isbn) }}">
                        @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Publisher Name</label>
                        <input type="text" name="publisher" class="form-control @error('publisher') is-invalid @enderror" 
                               placeholder="Enter publisher name" value="{{ old('publisher', $book->publisher) }}">
                        @error('publisher')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Publish Date</label>
                        <input type="date" name="publish_date" class="form-control @error('publish_date') is-invalid @enderror" 
                               value="{{ old('publish_date', $book->publish_date ? $book->publish_date->format('Y-m-d') : '') }}">
                        @error('publish_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Author</label>
                        <select name="author" class="form-select @error('author') is-invalid @enderror" required>
                            <option disabled>Select</option>
                            @foreach($authors as $author)
                            <option value="{{ $author }}" {{ old('author', $book->author) == $author ? 'selected' : '' }}>
                                {{ $author }}
                            </option>
                            @endforeach
                        </select>
                        @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Shelf Location</label>
                        <select name="shelf" class="form-select @error('shelf') is-invalid @enderror" disabled>
                            <option value="">Select</option>
                            @foreach($shelves as $shelf)
                                <option value="{{ $shelf->id }}"
                                    {{ (string) old('shelf', $book->shelf_id) === (string) $shelf->id ? 'selected' : '' }}>
                                    {{ $shelf->name }}
                                    @if($shelf->room && $shelf->room->library)
                                        ({{ $shelf->room->library->name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('shelf')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label">Owner</label>
                        <select name="owner" class="form-select @error('owner') is-invalid @enderror" disabled>
                            <option disabled>Select</option>
                            @foreach($owners as $owner)
                            <option value="{{ $owner }}" {{ old('owner', $book->owner) == $owner ? 'selected' : '' }}>
                                {{ $owner }}
                            </option>
                            @endforeach
                        </select>
                        @error('owner')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Available" {{ old('status', $book->status) == 'Available' ? 'selected' : '' }}>Available</option>
                            <option value="Borrowed" {{ old('status', $book->status) == 'Borrowed' ? 'selected' : '' }}>Borrowed</option>
                            <option value="Maintenance" {{ old('status', $book->status) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="Lost" {{ old('status', $book->status) == 'Lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Visibility</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="visibility" value="1" 
                               {{ old('visibility', $book->visibility) ? 'checked' : '' }}>
                        <label class="form-check-label">Make this book publicly visible</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Summary</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              placeholder="Brief description of the book">{{ old('description', $book->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-submit">Update Book</button>
                    <a href="{{ route('books.manage') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
