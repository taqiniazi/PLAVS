@extends('layouts.dashboard')

@section('title', 'Books in ' . $library->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h4 class="page-title">Books in {{ $library->name }}</h4>
            <a href="{{ route('libraries.other') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Libraries
            </a>
        </div>
        <div class="table-card">
            <div class="table-responsive">
                <table id="booksTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($book->image)
                                        <img src="{{ asset('storage/uploads/' . $book->image) }}" alt="{{ $book->title }}" class="rounded me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/book1.png') }}" alt="Default Cover" class="rounded me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                    @endif
                                    <span class="fw-bold">{{ $book->title }}</span>
                                </div>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->isbn }}</td>
                            <td>
                                <span class="badge bg-{{ strtolower($book->status) === 'available' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>
$(document).ready(function () {
    $('#booksTable').DataTable({
        "language": {
            "search": "Search books:",
        }
    });
});
</script>
@endpush
