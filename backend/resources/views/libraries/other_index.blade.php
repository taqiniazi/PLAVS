@extends('layouts.dashboard')

@section('title', 'Other Libraries')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
<style>
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4">
            <h4 class="page-title">Other Libraries</h4>
        </div>
        <div class="table-card">
            <div class="table-responsive">
                <table id="otherLibrariesTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Library Name</th>
                            <th>Address</th>
                            <th>Phone No</th>
                            <th>Email Address</th>
                            <th>Total Books</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($libraries as $library)
                        <tr class="clickable-row" onclick="window.location='{{ route('libraries.other.books', $library) }}'">
                            <td>
                                <span class="fw-bold">{{ $library->name }}</span>
                            </td>
                            <td>{{ $library->location ?? '-' }}</td>
                            <td>{{ $library->contact_phone ?? $library->owner->phone ?? '-' }}</td>
                            <td>{{ $library->contact_email ?? $library->owner->email ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $library->books_count }}</span>
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
    $('#otherLibrariesTable').DataTable({
        "language": {
            "search": "Search libraries:",
        }
    });
});
</script>
@endpush
