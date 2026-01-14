@extends('layouts.dashboard')

@section('title', 'Permissions Management')

@section('content')
<div class="container-fluid py-4">
    <h4 class="page-title">Permissions</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Owner Role Requests</h5>
        </div>
        <div class="card-body">
            @if($ownerRequests->isEmpty())
                <p class="text-muted mb-0">No owner role requests at the moment.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Library Name</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Phone</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ownerRequests as $req)
                                <tr>
                                    <td>{{ $req->user->name }}</td>
                                    <td>{{ $req->user->email }}</td>
                                    <td>{{ $req->library_name }}</td>
                                    <td>{{ $req->library_city ?? '-' }}</td>
                                    <td>{{ $req->library_country ?? '-' }}</td>
                                    <td>{{ $req->library_phone ?? '-' }}</td>
                                    <td>{{ $req->created_at->format('M d, Y') }}</td>
                                    <td class="d-flex gap-2">
                                        <form action="{{ route('permissions.owner-requests.approve', $req) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success">
                                                <i class="fas fa-user-check me-1"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('permissions.owner-requests.reject', $req) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-times me-1"></i> Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Candidates</h5>
        </div>
        <div class="card-body">
            @if($candidates->isEmpty())
                <p class="text-muted mb-0">No candidates found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Requested Owner</th>
                                <th>Assign Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($candidates as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @php
                                            $hasPendingOwnerRequest = \App\Models\OwnerRequest::where('user_id', $user->id)->where('status', 'pending')->exists();
                                        @endphp
                                        @if($hasPendingOwnerRequest)
                                            <span class="badge bg-warning text-dark">Requested</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('permissions.assign-role') }}" method="POST" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <select name="role" class="form-select form-select-sm" style="max-width: 220px;">
                                                <option value="owner">Owner</option>
                                                <option value="librarian">Librarian</option>
                                                <option value="public">Public</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="fas fa-user-tag me-1"></i> Assign
                                            </button>
                                        </form>
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
@include('permissions.partials.scripts')
@endsection
