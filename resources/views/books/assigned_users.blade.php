@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Assigned Users')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4">
            <h4 class="page-title">{{ $book->title }}</h4>
        </div>
        <div class="table-card">
            <div class="table-responsive">
                <table id="assignedUsersTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Assigned At</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($currentHolder)
                        <tr>
                            <td>{{ $currentHolder->name }}</td>
                            <td>{{ $currentHolder->role }}</td>
                            <td>{{ optional($book->updated_at)->format('Y-m-d H:i') }}</td>
                            <td></td>
                        </tr>
                        @endif
                        @foreach($assignedUsers as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->role }}</td>
                            <td>{{ optional($u->pivot->assigned_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $u->pivot->notes }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>
$(document).ready(function () {
    $('#assignedUsersTable').DataTable({
        "language": {
            "search": "Search:",
            "lengthMenu": "Display _MENU_ records per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ records"
        }
    });
});
</script>
@endpush
