@extends('layouts.dashboard')

@section('title', 'Libraries')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->hasAdminRole();
    $isSuperAdmin = $user->isSuperAdmin();
    $isLibrarian = $user->isLibrarian();
@endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4">
            <h4 class="page-title">Libraries</h4>
            @can('create', App\Models\Library::class)
            <div class="float-end">
                <a href="{{ route('libraries.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Library
                </a>
                <a href="javascript:;" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Librarian
                </a>
            </div>
            @endcan
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table id="librariesTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Library Name</th>
                            <th>Location</th>
                            <th>Map</th>
                            <th>Owner Name</th>
                            <th>Total Books</th>
                            <th class="text-end" data-orderable="false">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($libraries as $library)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $library->name }}</h6>
                                        @if($library->type)
                                        <small class="text-muted">
                                            <span class="badge {{ $library->type === 'public' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($library->type) }}
                                            </span>
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $library->location ?? 'Not specified' }}
                            </td>
                            <td>
                                @if($library->location)
                                <a href="https://maps.google.com/?q={{ urlencode($library->location) }}"
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-map-marker"></i> View Map
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{ $library->owner ? $library->owner->name : 'System' }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $library->books_count }}</span>
                            </td>
                            <td class="text-end">
                                @can('view', $library)
                                <a href="{{ route('libraries.show', $library) }}" class="btn-action btn-view" data-bs-toggle="tooltip" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('update', $library)
                                <a href="{{ route('libraries.edit', $library) }}" class="btn-action btn-edit" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete', $library)
                                <form method="POST" action="{{ route('libraries.destroy', $library) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this library? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-dispose" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
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
    // Initialize DataTables
    var table = $('#librariesTable').DataTable({
        "language": {
            "search": "Search libraries:",
            "lengthMenu": "Display _MENU_ libraries per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ libraries"
        },
        "columnDefs": [
            { "orderable": false, "targets": [2, 5] }
        ]
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush