@extends('layouts.dashboard')

@section('title', 'Event Attendees')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')

<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title mb-1">
                    <i class="fas fa-users me-2"></i>
                    Attendees - {{ $event->title }}
                </h4>
                <p class="text-muted mb-0">
                    {{ $event->start_date->format('d M Y, h:i A') }} &ndash; {{ $event->end_date->format('d M Y, h:i A') }}
                </p>
            </div>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Events
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Event Details</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Location:</strong> {{ $event->location ?? '-' }}</p>
                    <p class="mb-2"><strong>Speakers:</strong> {{ $event->speakers ?? '-' }}</p>
                    <p class="mb-2">
                        <strong>Fee:</strong>
                        @if(!is_null($event->fee_amount))
                            {{ $event->fee_currency ?? 'PKR' }} {{ number_format($event->fee_amount, 2) }}
                        @else
                            Free
                        @endif
                    </p>
                    @if(!is_null($event->fee_amount))
                        <hr>
                        <p class="mb-1"><strong>Bank Name:</strong> {{ $event->bank_name ?? '-' }}</p>
                        <p class="mb-0"><strong>IBAN / Account No:</strong> {{ $event->bank_account ?? '-' }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Registrations</h5>
                    <span class="badge bg-primary">{{ $registrations->count() }} total</span>
                </div>
                <div class="card-body">
                    @if($registrations->isEmpty())
                        <p class="text-muted mb-0">No one has registered for this event yet.</p>
                    @else
                        <div class="table-responsive">
                            <table id="registrationsTable" class="table table-hover align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Registered At</th>
                                        <th>Payment Proof</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations as $registration)
                                        <tr>
                                            <td>{{ $registration->name }}</td>
                                            <td>{{ $registration->email }}</td>
                                            <td>
                                                @if($registration->status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($registration->status === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $registration->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                @if($registration->payment_proof_path)
                                                    <a href="{{ asset('storage/'.$registration->payment_proof_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        View Proof
                                                    </a>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($registration->status === 'pending')
                                                    <form action="{{ route('events.registrations.update', [$event, $registration]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('events.registrations.update', [$event, $registration]) }}" method="POST" class="d-inline ms-1">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small">No actions</span>
                                                @endif
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
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var table = document.getElementById('registrationsTable');
    if (table) {
        $('#registrationsTable').DataTable({
            language: {
                search: '',
                lengthMenu: '_MENU_',
                info: 'Showing _START_ to _END_ of _TOTAL_ attendees'
            },
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    }
});
</script>
@endpush

