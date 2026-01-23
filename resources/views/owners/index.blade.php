@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Book Owners')

@push('styles')
<!-- <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}"> -->
@endpush

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4">
            <h4 class="page-title">Book Owners</h4>
        </div>
        <div class="table-card">
            <div class="table-responsive">
                <table id="ownersTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th width="40" data-orderable="false"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                            <th width="25%">Owner Details</th>
                            <th>Role</th>
                            <th>Books Owned</th>
                            <th>Joined Date</th>
                            <th class="text-end" data-orderable="false">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($owners as $owner)
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $owner['avatar'] }}" alt="Avatar" class="rounded-circle me-3" width="45" height="45" style="object-fit: cover;">
                                    <div>
                                        <span class="fw-bold d-block">{{ $owner['name'] }}</span>
                                        <span class="text-muted small">{{ $owner['email'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $owner['role'] }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">{{ $owner['books_count'] }}</span> books
                            </td>
                            <td>{{ $owner['joined_date'] }}</td>
                            <td class="text-end">
                                <a href="{{ route('owners.show', $owner['id']) }}" class="btn-action btn-assign" data-bs-toggle="tooltip" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('books.index', ['owner_id' => $owner['id']]) }}" class="btn-action btn-transfer" data-bs-toggle="tooltip" title="View Books">
                                    <i class="fas fa-book"></i>
                                </a>
                                <button type="button" class="btn-action btn-shelves bg-success border-0" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#sendMessageModal" 
                                        data-recipient-id="{{ $owner['id'] }}" 
                                        data-recipient-name="{{ $owner['name'] }}"
                                        title="Send Message">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('owners.send_message') }}" method="POST">
            @csrf
            <input type="hidden" name="recipient_id" id="recipient_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendMessageModalLabel">Send Message to <span id="recipient_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>

<script>
$(document).ready(function () {
    var table = $('#ownersTable').DataTable({
        "language": {
            "search": "Search Owners:",
            "lengthMenu": "Display _MENU_ owners per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ owners"
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

    // Handle Send Message Modal
    var sendMessageModal = document.getElementById('sendMessageModal');
    sendMessageModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var recipientId = button.getAttribute('data-recipient-id');
        var recipientName = button.getAttribute('data-recipient-name');
        
        var modalTitle = sendMessageModal.querySelector('.modal-title span');
        var recipientIdInput = sendMessageModal.querySelector('#recipient_id');
        
        modalTitle.textContent = recipientName;
        recipientIdInput.value = recipientId;
    });
});
</script>
@endpush
