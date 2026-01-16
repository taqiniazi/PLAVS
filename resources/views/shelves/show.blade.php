@extends('layouts.dashboard')

@section('title', 'Shelf Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
@endpush

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="row mt-3">
    <div class="col-12 mb-3">
        <a href="{{ route('shelves.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Shelves
        </a>
    </div>
</div>

<div class="row mt-2">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bookmark me-2"></i>
                    {{ $shelf->name }}
                </h5>
            </div>
            <div class="card-body">
                @if($shelf->code)
                    <p class="mb-2">
                        <span class="text-muted">Code:</span>
                        <strong>{{ $shelf->code }}</strong>
                    </p>
                @endif

                <p class="mb-2">
                    <span class="text-muted">Library:</span>
                    <strong>{{ optional(optional($shelf->room)->library)->name ?? 'N/A' }}</strong>
                </p>

                <p class="mb-2">
                    <span class="text-muted">Room:</span>
                    <strong>{{ optional($shelf->room)->name ?? 'N/A' }}</strong>
                </p>

                @if($shelf->description)
                    <p class="mb-0 mt-2">{{ $shelf->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>
                    Books on this Shelf ({{ $shelf->books->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($shelf->books->isEmpty())
                    <p class="text-muted mb-0">
                        No books are currently placed on this shelf.
                    </p>
                @else
                    <div class="table-responsive">
                        <table id="shelfBooksTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>In Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedBooks as $entry)
                                    @php $book = $entry['book']; @endphp
                                    <tr>
                                        <td>{{ $book->title }}</td>
                                        <td>{{ $book->author }}</td>
                                        <td>
                                            @if($entry['in_stock'] > 0)
                                                <span class="badge bg-success">{{ $entry['in_stock'] }}</span>
                                            @else
                                                <span class="badge bg-secondary">0</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function () {
    $('#shelfBooksTable').DataTable({
        "language": {
            "search": "Search books:",
            "lengthMenu": "Display _MENU_ books per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ books"
        }
    });
});
</script>
@endpush
