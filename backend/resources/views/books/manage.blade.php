@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Manage Books')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->hasAdminRole();
    $isTeacher = $user->isTeacher();
    $isStudent = $user->isStudent();
@endphp
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
            @if($isAdmin)
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
            @endif
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
                            @if($isAdmin)
                            <th width="40" data-orderable="false"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                            @endif
                            <th width="30%">Book Details</th>
                            <th>Shelf Location</th>
                            <th>Visibility</th>
                            <th>Owner / Status</th>
                            @if($isAdmin || $isTeacher || $isStudent)
                            <th class="text-end" data-orderable="false">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                        <tr>
                            <td><input type="checkbox" class="form-check-input book-checkbox"></td>
                            <td>
                                <div class="book-info">
                                    <img src="{{ $book->cover_url }}"
                                         alt="Cover" class="img-fluid">
                                    <div>
                                        <span class="book-title">{{ $book->title }}</span>
                                        <span class="book-author">ISBN: {{ $book->isbn ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($book->shelf)
                                    <span class="badge-shelf">
                                        {{ $book->shelf->room->library->name }} > 
                                        {{ $book->shelf->room->name }} > 
                                        {{ $book->shelf->name }}
                                    </span>
                                @else
                                    <span class="badge-shelf">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($isAdmin)
                                <div class="form-check form-switch">
                                    <input class="form-check-input visibility-toggle" type="checkbox" 
                                           {{ $book->visibility ? 'checked' : '' }} 
                                           data-book-id="{{ $book->id }}">
                                    <label class="form-check-label visibility-label">{{ $book->visibility ? 'Public' : 'Private' }}</label>
                                </div>
                                @else
                                    <span class="badge {{ $book->visibility ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $book->visibility ? 'Public' : 'Private' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $book->owner }}</span>
                                    <span class="badge-status-{{ str_contains($book->status, 'Available') ? 'avail' : 'borrowed' }} mt-1 w-auto">{{ $book->status }}</span>
                                </div>
                            </td>
                            <td class="text-end">
                                @if($isAdmin)
                                    <a href="{{ route('books.edit', $book) }}" class="btn btn-action btn-dark btn-edit" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn-action btn-assign" data-bs-toggle="tooltip" title="Assign" 
                                            data-id="{{ $book->id }}" data-title="{{ $book->title }}">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                    <button class="btn-action btn-transfer" data-bs-toggle="tooltip" title="Transfer" 
                                            data-id="{{ $book->id }}" data-title="{{ $book->title }}">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <button class="btn-action btn-shelves bg-dark" data-bs-toggle="tooltip" title="Change Shelf" 
                                            data-id="{{ $book->id }}" data-title="{{ $book->title }}">
                                        <i class="fa-solid fa-arrow-down-up-across-line"></i>
                                    </button>
                                    @if(str_contains($book->status, 'Borrowed'))
                                    <form method="POST" action="{{ route('books.return') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <input type="hidden" name="user_id" value="{{ $book->user_id }}">
                                        <button type="submit" class="btn-action btn-return text-white" data-bs-toggle="tooltip" title="Return Book"
                                                data-id="{{ $book->id }}" data-title="{{ $book->title }}" data-user-id="{{ $book->user_id }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form method="POST" action="{{ route('books.destroy', $book) }}" style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to DISPOSE of this book? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-dispose" data-bs-toggle="tooltip" title="Dispose">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @elseif($isTeacher)
                                    {{-- Teachers can view and assign to students --}}
                                    <button class="btn-action btn-assign" data-bs-toggle="tooltip" title="Assign to Student" 
                                            data-id="{{ $book->id }}" data-title="{{ $book->title }}">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                @elseif($isStudent)
                                    {{-- Students can only view book details via clicking --}}
                                    {{-- No action buttons needed --}}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Single Transfer Modal (Outside Loop) -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i> Transfer Ownership</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transferForm" method="POST" action="">
                @csrf
                <input type="hidden" name="book_id" id="transfer_book_id">
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="transferDescription">Select a new owner for this book.</p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">New Owner</label>
                        <select name="owner" class="form-select" required>
                            <option selected disabled>Select User...</option>
                            @foreach($users as $userOption)
                                @if($userOption->id !== $user->id)
                                <option value="{{ $userOption->name }}">{{ $userOption->name }}</option>
                                @endif
                            @endforeach
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

<!-- Single Shelves Modal (Outside Loop) -->
<div class="modal fade" id="shelvesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i> Change Shelves</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shelvesForm" method="POST" action="">
                @csrf
                <input type="hidden" name="book_id" id="shelves_book_id">
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="shelvesDescription">Select a new shelf location for this book.</p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Transfer to Shelf</label>
                        <select name="shelf_location" class="form-select" required>
                            <option selected disabled>Select Shelf...</option>
                            @foreach($shelves ?? [] as $shelf)
                            <option value="{{ $shelf->name }}">{{ $shelf->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Reason (Optional)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="e.g. Reorganization"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modal-save">Confirm Change</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Single Assign Modal (Outside Loop) -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Assign Book</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm" method="POST" action="">
                @csrf
                <input type="hidden" name="book_id" id="assign_book_id">
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="assignDescription">Assign this book to a user.</p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Assign To</label>
                        <select name="assigned_user_id" class="form-select" required>
                            <option selected disabled>Select User...</option>
                            @foreach($users as $userOption)
                                <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
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

<!-- Single Return Modal (Outside Loop) -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-undo me-2"></i> Return Book</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="returnForm" method="POST" action="">
                @csrf
                <input type="hidden" name="book_id" id="return_book_id">
                <input type="hidden" name="user_id" id="return_user_id">
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="returnDescription">Confirm the return of this book.</p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Condition (Optional)</label>
                        <select name="condition" class="form-select">
                            <option value="">Select Condition...</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Any damage or remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modal-save">Confirm Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>

<script>
$(document).ready(function () {
    // Initialize DataTables
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
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('tbody input[type="checkbox"].book-checkbox');
        for(var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = this.checked;
        }
    });

    // Move modals to body to fix z-index issues
    $('.modal').appendTo('body');

    // ========== TRANSFER MODAL LOGIC ==========
    $(document).on('click', '.btn-transfer', function () {
        var bookId = $(this).data('id');
        var bookTitle = $(this).data('title');
        
        // Update modal content
        $('#transferDescription').text('Transferring ownership of "' + bookTitle + '" to:');
        $('#transfer_book_id').val(bookId);
        $('#transferForm').attr('action', "{{ route('books.transfer') }}");
        
        // Show modal
        var transferModal = new bootstrap.Modal(document.getElementById('transferModal'));
        transferModal.show();
    });

    // ========== SHELVES MODAL LOGIC ==========
    $(document).on('click', '.btn-shelves', function () {
        var bookId = $(this).data('id');
        var bookTitle = $(this).data('title');
        
        // Update modal content
        $('#shelvesDescription').text('Change shelf location of "' + bookTitle + '" to:');
        $('#shelves_book_id').val(bookId);
        $('#shelvesForm').attr('action', "{{ route('books.change_shelf') }}");
        
        // Show modal
        var shelvesModal = new bootstrap.Modal(document.getElementById('shelvesModal'));
        shelvesModal.show();
    });

    // Handle Shelves Form Submission via AJAX
    $('#shelvesForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalBtnText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Close modal
                var modalEl = document.getElementById('shelvesModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // Update table cell dynamically
                var bookId = response.book_id;
                var newShelfName = response.new_shelf_name;

                // Find the row with this book and update the shelf location cell
                $('#booksTable tbody tr').each(function() {
                    var row = $(this);
                    // Look for the shelves button with this book ID
                    var shelvesBtn = row.find('.btn-shelves[data-id="' + bookId + '"]');
                    if (shelvesBtn.length) {
                        // Update shelf cell - it's the 3rd cell (index 2)
                        var shelfCell = row.find('td:eq(2)');
                        shelfCell.html('<span class="badge-shelf">' + newShelfName + '</span>');
                    }
                });

                // Show success message
                $('body').append('<div class="alert alert-success alert-dismissible fade show" role="alert" style="position:fixed;top:20px;right:20px;z-index:9999;">' +
                    response.message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                // Remove alert after 3 seconds
                setTimeout(function() {
                    $('.alert').fadeOut(300, function() { $(this).remove(); });
                }, 3000);
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // ========== ASSIGN MODAL LOGIC ==========
    $(document).on('click', '.btn-assign', function () {
        var bookId = $(this).data('id');
        var bookTitle = $(this).data('title');
        
        // Update modal content
        $('#assignDescription').text('Assign "' + bookTitle + '" to:');
        $('#assign_book_id').val(bookId);
        $('#assignForm').attr('action', "{{ route('books.assign') }}");
        
        // Show modal
        var assignModal = new bootstrap.Modal(document.getElementById('assignModal'));
        assignModal.show();
    });

    // ========== RETURN MODAL LOGIC ==========
    $(document).on('click', '.btn-return', function () {
        var bookId = $(this).data('id');
        var bookTitle = $(this).data('title');
        var userId = $(this).data('user-id');
        
        // Update modal content
        $('#returnDescription').text('Confirm return of "' + bookTitle + '" by the borrower.');
        $('#return_book_id').val(bookId);
        $('#return_user_id').val(userId);
        $('#returnForm').attr('action', "{{ route('books.return') }}");
        
        // Show modal
        var returnModal = new bootstrap.Modal(document.getElementById('returnModal'));
        returnModal.show();
    });

    // Handle Return Form Submission via AJAX
    $(document).on('submit', 'form[action="{{ route("books.return") }}"]', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalBtnText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Update table cell dynamically - find the row and update status
                var bookId = response.book_id;
                
                // Find the row with this book and update status
                $('#booksTable tbody tr').each(function() {
                    var row = $(this);
                    // Look for the return button with this book ID
                    var returnBtn = row.find('.btn-return[data-id="' + bookId + '"]');
                    if (returnBtn.length) {
                        // Update status cell - it's the 4th cell (index 4)
                        var statusCell = row.find('td:eq(4)');
                        var statusBadge = statusCell.find('.badge');
                        if (statusBadge.length) {
                            statusBadge.removeClass('badge-borrowed').addClass('badge-avail').text('Available');
                        }
                        // Remove the return button and its form
                        returnBtn.closest('form').remove();
                    }
                });
                
                // Show success message
                $('body').append('<div class="alert alert-success alert-dismissible fade show" role="alert" style="position:fixed;top:20px;right:20px;z-index:9999;">' +
                    response.message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                
                // Remove alert after 3 seconds
                setTimeout(function() {
                    $('.alert').fadeOut(300, function() { $(this).remove(); });
                }, 3000);
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // ========== VISIBILITY TOGGLE LOGIC ==========
    $(document).on('change', '.visibility-toggle', function() {
        var bookId = $(this).data('book-id');
        var isVisible = $(this).is(':checked');
        var label = $(this).siblings('.visibility-label');
        var checkbox = $(this);
        
        // Update label immediately for better UX
        label.text(isVisible ? 'Public' : 'Private');
        
        // Send AJAX request to update visibility
        $.ajax({
            url: '/books/' + bookId + '/toggle-visibility',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                visibility: isVisible ? 1 : 0
            },
            success: function(response) {
                console.log('Visibility updated successfully');
                // Update the label text to match the new state
                label.text(isVisible ? 'Public' : 'Private');
            },
            error: function(xhr) {
                // Revert the toggle if there's an error
                checkbox.prop('checked', !isVisible);
                label.text(!isVisible ? 'Public' : 'Private');
                alert('Error updating visibility. Please try again.');
            }
        });
    });
});
</script>
@endpush
