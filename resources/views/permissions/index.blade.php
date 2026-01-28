@extends('layouts.dashboard')

@section('title', 'Permissions Management')

@section('content')
<div class="container-fluid py-4">
    <h4 class="page-title mb-3">Permissions</h4>

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
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Payment</th>
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
                                    <td>{{ Str::limit($req->library_address, 20) ?? '-' }}</td>
                                    <td>{{ $req->library_phone ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-light text-dark border">{{ ucfirst($req->payment_method) }}</span>
                                            @if($req->transaction_screenshot_path)
                                                <button type="button" 
                                                        class="btn btn-link ms-2 text-primary p-0 border-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#screenshotModal"
                                                        data-image-url="{{ asset('storage/' . $req->transaction_screenshot_path) }}"
                                                        title="View Screenshot">
                                                    <i class="fas fa-file-invoice fa-lg"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
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
            <h5 class="mb-0">Users List</h5>
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
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($candidates as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->getRoleDisplayName() }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editRoleModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $user->role }}"
                                        >
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
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

<!-- Screenshot Modal -->
<div class="modal fade" id="screenshotModal" tabindex="-1" aria-labelledby="screenshotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="screenshotModalLabel">Transaction Screenshot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center bg-light">
                <img id="modalScreenshotImage" src="" alt="Transaction Screenshot" class="img-fluid rounded shadow-sm" style="max-height: 80vh;">
            </div>
            <div class="modal-footer">
                <a id="downloadScreenshotLink" href="" download class="btn btn-primary" target="_blank">
                    <i class="fas fa-download me-1"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('permissions.assign-role') }}" id="editRoleForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit User Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_role_user_id">
                    <p class="mb-3">
                        <strong>User:</strong>
                        <span id="edit_role_user_name"></span>
                        <br>
                        <strong>Email:</strong>
                        <span id="edit_role_user_email"></span>
                    </p>
                    <div class="mb-3">
                        <label for="edit_role_select" class="form-label">Role</label>
                        <select name="role" id="edit_role_select" class="form-select">
                            <option value="{{ \App\Models\User::ROLE_OWNER }}">Owner</option>
                            <option value="{{ \App\Models\User::ROLE_LIBRARIAN }}">Librarian</option>
                            <option value="{{ \App\Models\User::ROLE_PUBLIC }}">Public</option>
                            <option value="{{ \App\Models\User::ROLE_ADMIN }}">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('permissions.partials.scripts')
@endsection
