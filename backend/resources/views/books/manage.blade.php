@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Manage Books')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4">
            <h4 class="page-title">Manage Books</h4>
            <form method="GET" action="{{ route('books.manage') }}" class="d-flex align-items-center gap-2">
                <div class="custom-search">
                    <input type="text" name="search" placeholder="Search books..." 
                           value="{{ request('search') }}">
                    <i class="fas fa-search"></i>
                </div>
                @if(request('search'))
                    <a href="{{ route('books.manage') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </form>
        </div>
        
        @if(request('search'))
        <div class="alert alert-info mb-3">
            <i class="fas fa-search me-2"></i>
            Showing {{ $books->count() }} results for "<strong>{{ request('search') }}</strong>"
        </div>
        @endif
        
        <div class="table-card">
            <div class="table-responsive">
                <table id="booksTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th width="40" data-orderable="false"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                            <th width="30%">Book Details</th>
                            <th>Shelf</th>
                            <th>Visibility</th>
                            <th>Owner / Status</th>
                            <th class="text-end" data-orderable="false">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <div class="book-info">
                                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/' . ($book->image ?? 'book1.png')) }}" 
                                         alt="Cover" class="img-fluid">
                                    <div>
                                        <span class="book-title">{{ $book->title }}</span>
                                        <span class="book-author">ISBN: {{ $book->isbn ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge-shelf">{{ $book->shelf_location }}</span></td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input visibility-toggle" type="checkbox" 
                                           {{ $book->visibility ? 'checked' : '' }} 
                                           data-book-id="{{ $book->id }}">
                                    <label class="form-check-label visibility-label">{{ $book->visibility ? 'Public' : 'Private' }}</label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $book->owner }}</span>
                                    <span class="badge-status-{{ str_contains($book->status, 'Available') ? 'avail' : 'borrowed' }} mt-1 w-auto">{{ $book->status }}</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('books.edit', $book) }}" class="btn-action btn-edit" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn-action btn-assign" data-bs-toggle="tooltip" title="Assign" data-book-id="{{ $book->id }}" data-book-title="{{ $book->title }}">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-action btn-transfer" data-bs-toggle="tooltip" title="Transfer" 
                                        data-book-id="{{ $book->id }}" data-book-title="{{ $book->title }}">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <button class="btn-action btn-shelves bg-dark" data-bs-toggle="tooltip" title="Change Shelf" 
                                        data-book-id="{{ $book->id }}" data-book-title="{{ $book->title }}">
                                    <i class="fa-solid fa-arrow-down-up-across-line"></i>
                                </button>
                                <form method="POST" action="{{ route('books.destroy', $book) }}" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to DISPOSE of this book? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-dispose" data-bs-toggle="tooltip" title="Dispose">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i> Transfer Ownership</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transferForm" method="POST" action="{{ route('books.transfer') }}">
                @csrf
                <input type="hidden" name="book_id" id="transfer_book_id">
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="transferDescription">Select a new owner for this book. The current owner will lose access rights.</p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">New Owner</label>
                        <select name="owner" class="form-select" required>
                            <option selected disabled>Select User...</option>
                            <option value="Sarah Ahmed">Sarah Ahmed</option>
                            <option value="Ali Khan">Ali Khan</option>
                            <option value="Library Admin">Library Admin</option>
                            <option value="Taqi Raza Khan">Taqi Raza Khan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Reason (Optional)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="e.g. Donation, Sale"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modal-save">Confirm Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Shelves Modal -->
<div class="modal fade" id="shelvesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i> Change Shelves</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shelvesForm" method="POST" action="{{ route('books.change_shelf') }}">
                @csrf
                <input type="hidden" name="book_id" id="shelves_book_id">
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="shelvesDescription"></p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Transfer to Shelf</label>
                        <select name="shelf_location" class="form-select" required>
                            <option selected disabled>Select Shelf...</option>
                            <option value="Shelf A-1">Shelf A-1</option>
                            <option value="Shelf A-2">Shelf A-2</option>
                            <option value="Shelf B-1">Shelf B-1</option>
                            <option value="Shelf B-2">Shelf B-2</option>
                            <option value="Shelf C-1">Shelf C-1</option>
                            <option value="Shelf C-2">Shelf C-2</option>
                            <option value="Shelf C-3">Shelf C-3</option>
                            <option value="Shelf C-4">Shelf C-4</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Reason (Optional)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="e.g. Reorganization"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modal-save">Confirm Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Assign Book</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm" method="POST" action="{{ route('books.assign') }}">
                @csrf
                <input type="hidden" name="book_id" id="assign_book_id">
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="assignDescription">Assign this book to a user</p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Assign To</label>
                        <select name="assigned_user_id" class="form-select" required>
                            <option selected disabled>Select User...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Notes (Optional)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="e.g. For Review"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modal-save">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    var table = $('#booksTable').DataTable({
        "language": {
            "search": "Search Inventory:",
            "lengthMenu": "Display _MENU_ books per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ books"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 5] } 
        ]
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Select all checkbox functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        for(var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = this.checked;
        }
    });

    // Transfer modal functionality
    $(document).on('click', '.btn-transfer', function () {
        var bookId = $(this).data("book-id");
        var bookTitle = $(this).data("book-title");
        
        $('#transferDescription').text('Transferring the Ownership of "' + bookTitle + '" to:');
        $('#transfer_book_id').val(bookId);
        $('#transferForm').attr('action', "{{ route('books.transfer') }}");
        
        var transferModal = new bootstrap.Modal(document.getElementById('transferModal'));
        transferModal.show();
    });

    // Shelves modal functionality
    $(document).on('click', '.btn-shelves', function () {
        var bookId = $(this).data("book-id");
        var bookTitle = $(this).data("book-title");
        
        $('#shelvesDescription').text('Change the shelf of "' + bookTitle + '" to:');
        $('#shelves_book_id').val(bookId);
        $('#shelvesForm').attr('action', '{{ route('books.change_shelf') }}');
        
        var shelvesModal = new bootstrap.Modal(document.getElementById('shelvesModal'));
        shelvesModal.show();
    });

    // Assign modal functionality
    $(document).on('click', '.btn-assign', function () {
        var bookId = $(this).data("book-id");
        var bookTitle = $(this).data("book-title");

        $('#assignDescription').text('Assign "' + bookTitle + '" to:');
        $('#assign_book_id').val(bookId);
        $('#assignForm').attr('action', '{{ route('books.assign') }}');

        var assignModal = new bootstrap.Modal(document.getElementById('assignModal'));
        assignModal.show();
    });

    // Visibility toggle functionality
    $(document).on('change', '.visibility-toggle', function() {
        var bookId = $(this).data('book-id');
        var isVisible = $(this).is(':checked');
        var label = $(this).siblings('.visibility-label');
        
        // Update label immediately for better UX
        label.text(isVisible ? 'Public' : 'Private');
        
        // Send AJAX request to update visibility
        $.ajax({
            url: '/books/' + bookId,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                visibility: isVisible ? 1 : 0
            },
            success: function(response) {
                // Show success message or handle response
                console.log('Visibility updated successfully');
            },
            error: function(xhr) {
                // Revert the toggle if there's an error
                $(this).prop('checked', !isVisible);
                label.text(!isVisible ? 'Public' : 'Private');
                alert('Error updating visibility. Please try again.');
            }
        });
    });
});
</script>
@endpush