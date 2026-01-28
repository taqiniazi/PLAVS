@extends('layouts.dashboard')

@section('title', 'PLAVS  - Owner Profile')

@section('content')
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <h4 class="page-title">Owner Profile</h4>
                <a href="{{ route('owners.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Back to Owners</a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/user.png') }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle mb-3 border" 
                         width="120" height="120" 
                         style="object-fit: cover;">
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge bg-primary px-3 py-2 mb-3">{{ ucfirst($user->role) }}</span>
                    
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#sendMessageModal" 
                                data-recipient-id="{{ $user->id }}" 
                                data-recipient-name="{{ $user->name }}">
                            <i class="fas fa-envelope me-2"></i>Send Message
                        </button>
                        <a href="{{ route('books.index', ['owner_id' => $user->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-book me-2"></i>View Books
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top p-3">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h5 class="mb-0 fw-bold">{{ $user->owned_books_count }}</h5>
                            <small class="text-muted">Books Owned</small>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-0 fw-bold">{{ $libraries->count() }}</h5>
                            <small class="text-muted">Libraries</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">Libraries Owned</h5>
                </div>
                <div class="card-body p-0">
                    @if($libraries->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($libraries as $library)
                        <div class="list-group-item px-4 py-3 border-bottom-0">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $library->name }}</h6>
                                    <p class="mb-1 text-muted small">{{ $library->location ?? 'No location specified' }}</p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark border">{{ $library->books_count }} Books</span>
                                    <a href="{{ route('libraries.other.books', $library) }}" class="btn btn-sm btn-link text-decoration-none ms-2">View</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-building fa-2x text-muted mb-3 opacity-50"></i>
                        <p class="text-muted">No libraries created yet.</p>
                        
                        @if((auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()) && isset($ownerRequest))
                        <div class="mt-4 text-start px-5">
                            <h6 class="fw-bold border-bottom pb-2">Submitted Library Details</h6>
                            <div class="row g-2 small">
                                <div class="col-md-4 text-muted">Library Name:</div>
                                <div class="col-md-8 fw-bold">{{ $ownerRequest->library_name }}</div>
                                
                                <div class="col-md-4 text-muted">Address:</div>
                                <div class="col-md-8">{{ $ownerRequest->library_address ?? 'N/A' }}</div>
                                
                                <div class="col-md-4 text-muted">City/Country:</div>
                                <div class="col-md-8">{{ $ownerRequest->library_city }}, {{ $ownerRequest->library_country }}</div>
                                
                                <div class="col-md-4 text-muted">Phone:</div>
                                <div class="col-md-8">{{ $ownerRequest->library_phone ?? 'N/A' }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
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
<script>
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
</script>
@endpush
