@extends('layouts.dashboard')

@section('title', 'PLAVS  - Manage Shelves')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/accordion/3.0.7/accordion.min.css"> --> 
@endpush

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->hasAdminRole();
@endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h4 class="page-title">Manage Shelves</h4>
            @can('create', App\Models\Shelf::class)
                <a href="{{ route('shelves.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Shelf
                </a>
            @endcan
        </div>

        <div class="table-card">
            <div class="accordion" id="shelvesAccordion">
                @forelse($shelvesByLibrary as $libraryName => $shelves)
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading{{ Str::slug($libraryName) }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ Str::slug($libraryName) }}" aria-expanded="false" 
                                    aria-controls="collapse{{ Str::slug($libraryName) }}">
                                <i class="fas fa-building me-2"></i>
                                <strong>{{ $libraryName }}</strong>
                                <span class="badge bg-secondary ms-2">{{ $shelves->count() }} Shelves</span>
                            </button>
                        </h2>
                        <div id="collapse{{ Str::slug($libraryName) }}" class="accordion-collapse collapse" 
                             aria-labelledby="heading{{ Str::slug($libraryName) }}" data-bs-parent="#shelvesAccordion">
                            <div class="accordion-body p-0">
                                @php
                                    $shelvesByRoom = $shelves->groupBy(fn($shelf) => $shelf->room->name);
                                @endphp
                                
                                <div class="accordion nested-accordion" id="roomAccordion{{ Str::slug($libraryName) }}">
                                    @foreach($shelvesByRoom as $roomName => $roomShelves)
                                        <div class="accordion-item border-0 border-bottom">
                                            <h3 class="accordion-header" id="roomHeading{{ Str::slug($libraryName . $roomName) }}">
                                                <button class="accordion-button collapsed py-3" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#roomCollapse{{ Str::slug($libraryName . $roomName) }}" 
                                                        aria-expanded="false" 
                                                        aria-controls="roomCollapse{{ Str::slug($libraryName . $roomName) }}">
                                                    <i class="fas fa-door-open me-2"></i>
                                                    {{ $roomName }}
                                                    <span class="badge bg-info ms-2">{{ $roomShelves->count() }}</span>
                                                </button>
                                            </h3>
                                            <div id="roomCollapse{{ Str::slug($libraryName . $roomName) }}" 
                                                 class="accordion-collapse collapse" 
                                                 aria-labelledby="roomHeading{{ Str::slug($libraryName . $roomName) }}"
                                                 data-bs-parent="#roomAccordion{{ Str::slug($libraryName) }}">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        @foreach($roomShelves as $shelf)
                                                            <div class="col-md-4 mb-3">
                                                                <div class="card h-100 shelf-card">
                                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                                        <h6 class="mb-0">
                                                                            <i class="fas fa-bookmark me-1"></i>
                                                                            {{ $shelf->name }}
                                                                        </h6>
                                                                        <div>
                                                                            @can('update', $shelf)
                                                                                <a href="#" 
                                                                                   class="btn btn-sm btn-outline-primary btn-edit-shelf" 
                                                                                   data-bs-toggle="tooltip" title="Edit"
                                                                                   data-shelf-id="{{ $shelf->id }}"
                                                                                   data-shelf-name="{{ $shelf->name }}"
                                                                                   data-library-id="{{ optional($shelf->room->library)->id }}">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                            @endcan
                                                                            @can('delete', $shelf)
                                                                                <form method="POST" 
                                                                                      action="{{ route('shelves.destroy', $shelf) }}" 
                                                                                      style="display: inline;"
                                                                                      onsubmit="return confirm('Are you sure you want to delete this shelf?')">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" 
                                                                                            class="btn btn-sm btn-outline-danger" 
                                                                                            data-bs-toggle="tooltip" title="Delete">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            @endcan
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        @if($shelf->code)
                                                                            <p class="text-muted small mb-2">
                                                                                <i class="fas fa-qrcode me-1"></i>
                                                                                Code: {{ $shelf->code }}
                                                                            </p>
                                                                        @endif
                                                                        @if($shelf->description)
                                                                            <p class="small mb-3">{{ $shelf->description }}</p>
                                                                        @endif
                                                                        
                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                            <span class="small text-muted">Books:</span>
                                                                            <span class="badge bg-primary">{{ $shelf->total_books }}</span>
                                                                        </div>
                                                                        
                                                                        @php
                                                                            $uniqueBooks = $shelf->books->groupBy(function ($book) {
                                                                                return $book->isbn ?: ($book->title.'|'.$book->author);
                                                                            })->map(function ($items) {
                                                                                return $items->first();
                                                                            })->values();
                                                                        @endphp
                                                                        @if($uniqueBooks->count() > 0)
                                                                            <div class="book-preview mt-2">
                                                                                <p class="small text-muted mb-1">Recent Books:</p>
                                                                                <ul class="list-unstyled small">
                                                                                    @foreach($uniqueBooks->take(3) as $book)
                                                                                        <li>
                                                                                            <i class="fas fa-book text-secondary me-1"></i>
                                                                                            {{ Str::limit($book->title, 30) }}
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                                @if($uniqueBooks->count() > 3)
                                                                                    <p class="small text-muted mb-0">
                                                                                        +{{ $uniqueBooks->count() - 3 }} more books
                                                                                    </p>
                                                                                @endif
                                                                            </div>
                                                                        @else
                                                                            <p class="small text-muted text-center py-3 mb-0">
                                                                                <i class="fas fa-book-open me-1"></i>
                                                                                No books on this shelf
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                    <div class="card-footer bg-transparent">
                                                                        <a href="{{ route('shelves.show', $shelf) }}" 
                                                                           class="btn btn-sm btn-outline-primary w-100">
                                                                            <i class="fas fa-eye me-1"></i> View All Books
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No shelves found.</p>
                        @can('create', App\Models\Shelf::class)
                            <a href="{{ route('shelves.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Your First Shelf
                            </a>
                        @endcan
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@can('create', App\Models\Shelf::class)
@php
    $canSelectLibrary = isset($libraries) && $libraries->count() > 1;
@endphp
<div class="modal fade" id="editShelfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Shelf</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editShelfForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Shelf Name</label>
                        <input type="text" class="form-control" name="name" id="edit_shelf_name" required>
                    </div>
                    @if($canSelectLibrary)
                        <div class="mb-3">
                            <label class="form-label">Library</label>
                            <select class="form-select" name="library_id" id="edit_library_id">
                                @foreach($libraries as $library)
                                    <option value="{{ $library->id }}">{{ $library->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/accordion/3.0.7/accordion.min.js"></script>
<script>
$(document).ready(function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    $('.btn-edit-shelf').on('click', function (e) {
        e.preventDefault();
        var button = $(this);
        var shelfId = button.data('shelf-id');
        var shelfName = button.data('shelf-name');
        var libraryId = button.data('library-id');
        var form = $('#editShelfForm');

        form.attr('action', '{{ route('shelves.update', ['shelf' => '__ID__']) }}'.replace('__ID__', shelfId));
        $('#edit_shelf_name').val(shelfName);

        var librarySelect = $('#edit_library_id');
        if (librarySelect.length) {
            if (libraryId) {
                librarySelect.val(String(libraryId));
            } else {
                librarySelect.val('');
            }
        }

        var modalEl = document.getElementById('editShelfModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    var editShelfId = '{{ request('edit_shelf') }}';
    if (editShelfId) {
        var triggerButton = document.querySelector('.btn-edit-shelf[data-shelf-id=\"' + editShelfId + '\"]');
        if (triggerButton) {
            triggerButton.click();
        }
    }
});
</script>
@endpush
