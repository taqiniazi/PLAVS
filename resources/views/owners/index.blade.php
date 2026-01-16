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
                                <button class="btn-action btn-assign" data-bs-toggle="tooltip" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-transfer" data-bs-toggle="tooltip" title="View Books">
                                    <i class="fas fa-book"></i>
                                </button>
                                <button class="btn-action btn-shelves bg-success" data-bs-toggle="tooltip" title="Send Message">
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
});
</script>
@endpush
