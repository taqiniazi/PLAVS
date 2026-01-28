@extends('layouts.dashboard')

@section('title', 'PLAVS - Events')

@push('styles')
@php
    $user = auth()->user();
    $isPublic = $user && $user->isPublic();
@endphp
@if($isPublic)
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@else
    <!-- <link href="{{ asset('css/fullcalendar.global.min.css') }}" rel='stylesheet' /> -->
     
@endif
@endpush

@section('content')
@php
    $user = auth()->user();
    $isPublic = $user && $user->isPublic();
@endphp

@if($isPublic)
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4">
            <h4 class="page-title">Events</h4>
        </div>

        <div class="table-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Upcoming Events</h5>
            </div>
            <div class="table-responsive">
                <table id="upcomingEventsTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Location</th>
                            <th>Speakers</th>
                            <th>Fee</th>
                            <th>Created By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($upcomingEvents ?? collect()) as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->start_date->format('d M Y, h:i A') }}</td>
                                <td>{{ $event->end_date->format('d M Y, h:i A') }}</td>
                                <td>{{ $event->location ?? '-' }}</td>
                                <td>{{ $event->speakers ?? '-' }}</td>
                                <td>
                                    @if(!is_null($event->fee_amount))
                                        <span class="badge bg-warning text-dark">
                                            {{ $event->fee_currency ?? 'PKR' }} {{ number_format($event->fee_amount, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">Free</span>
                                    @endif
                                </td>
                                <td>{{ optional($event->creator)->name ?? 'System' }}</td>
                                <td class="text-end">
                                    @php
                                        $registration = $event->registrations->first();
                                    @endphp

                                    @if($registration)
                                        @if($registration->status === 'approved')
                                            <span class="badge bg-success">Registered</span>
                                        @elseif($registration->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending Approval</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    @else
                                        <button 
                                            class="btn btn-sm btn-primary btn-register-event"
                                            data-bs-toggle="modal"
                                            data-bs-target="#eventRegisterModal"
                                            data-event-id="{{ $event->id }}"
                                            data-event-title="{{ $event->title }}"
                                            data-event-location="{{ $event->location ?? '' }}"
                                            data-event-speakers="{{ $event->speakers ?? '' }}"
                                            data-event-fee="{{ $event->fee_amount }}"
                                            data-event-fee-currency="{{ $event->fee_currency ?? 'PKR' }}"
                                            data-event-bank-name="{{ $event->bank_name ?? '' }}"
                                            data-event-bank-account="{{ $event->bank_account ?? '' }}"
                                        >
                                            Register
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Previous Events</h5>
            </div>
            <div class="table-responsive">
                <table id="pastEventsTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Location</th>
                            <th>Speakers</th>
                            <th>Fee</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($pastEvents ?? collect()) as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->start_date->format('d M Y, h:i A') }}</td>
                                <td>{{ $event->end_date->format('d M Y, h:i A') }}</td>
                                <td>{{ $event->location ?? '-' }}</td>
                                <td>{{ $event->speakers ?? '-' }}</td>
                                <td>
                                    @if(!is_null($event->fee_amount))
                                        <span class="badge bg-warning text-dark">
                                            {{ $event->fee_currency ?? 'PKR' }} {{ number_format($event->fee_amount, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">Free</span>
                                    @endif
                                </td>
                                <td>{{ optional($event->creator)->name ?? 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Event Registration Modal -->
<div class="modal fade" id="eventRegisterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check me-2"></i>
                    Register for Event
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="eventRegisterForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="mb-1 fw-bold" id="eventRegisterTitle"></p>
                        <p class="mb-0 small text-muted" id="eventRegisterMeta"></p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Your Name</label>
                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Phone (optional)</label>
                        <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional information"></textarea>
                    </div>

                    <div id="eventPaymentSection" class="mb-3" style="display:none;">
                        <div class="alert alert-info mb-2">
                            <div class="fw-bold mb-1">This event requires a fee.</div>
                            <div class="small" id="eventFeeText"></div>
                        </div>
                        <div class="border rounded p-2 mb-2 bg-light">
                            <p class="mb-1 small"><strong>Bank Name:</strong> <span id="eventBankName"></span></p>
                            <p class="mb-0 small"><strong>IBAN / Account No:</strong> <span id="eventBankAccount"></span></p>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium">Upload Payment Proof (screenshot/photo)</label>
                            <input type="file" name="payment_proof" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-1">Max size 4MB. Please upload clear proof of successful bank transfer.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@else
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h4 class="page-title mb-0">Events Calendar</h4>
            <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#eventModal">
                <i class="fas fa-plus me-2"></i>Add Event
            </button>
        </div>
        <div class="bg-white p-4 rounded-3 shadow-sm mb-4">
            <div id="calendar"></div>
        </div>
    </div>
</div>

@if(isset($myEvents))
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">My Events</h5>
            </div>
            <div class="table-responsive">
                <table id="myEventsTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Location</th>
                            <th>Fee</th>
                            <th class="text-end">Attendees</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myEvents as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->start_date->format('d M Y, h:i A') }}</td>
                                <td>{{ $event->end_date->format('d M Y, h:i A') }}</td>
                                <td>{{ $event->location ?? '-' }}</td>
                                <td>
                                    @if(!is_null($event->fee_amount))
                                        <span class="badge bg-warning text-dark">
                                            {{ $event->fee_currency ?? 'PKR' }} {{ number_format($event->fee_amount, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">Free</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('events.attendees', $event) }}" class="btn btn-sm btn-outline-primary">
                                        View Attendees
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Add New Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="eventForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Event Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter event title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Event description (optional)"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Location <small class="text-muted" style="font-size:10px">(optional)</small></label>
                            <input type="text" name="location" class="form-control" placeholder="Event location">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Speakers <small class="text-muted" style="font-size:10px">(optional)</small></label>
                            <input type="text" name="speakers" class="form-control" placeholder="Main speakers">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Start Date & Time</label>
                            <input type="datetime-local" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">End Date & Time</label>
                            <input type="datetime-local" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Color</label>
                        <div class="d-flex gap-2">
                            <input type="color" name="color" class="form-control form-control-color" value="#007bff" style="width: 60px;">
                            <input type="text" class="form-control" value="#007bff" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Event Fee</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="number" step="0.01" min="0" name="fee_amount" class="form-control" placeholder="Amount">
                                <small class="text-muted" style="font-size:10px">Leave amount empty for free events.</small>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="fee_currency" class="form-control" value="PKR" placeholder="Currency">
                            </div>
                           
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Bank Name <br><small class="text-muted" style="font-size:10px">Required if fee is set.</small></label>
                            <input type="text" name="bank_name" class="form-control" placeholder="Bank name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">IBAN / Account No <br><small class="text-muted" style="font-size:10px">Required if fee is set.</small></label>
                            <input type="text" name="bank_account" class="form-control" placeholder="IBAN or account number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modal-save">Add Event</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
@php
    $user = auth()->user();
    $isPublic = $user && $user->isPublic();
@endphp

@if($isPublic)
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var upcomingTable = document.getElementById('upcomingEventsTable');
    if (upcomingTable) {
        $('#upcomingEventsTable').DataTable({
            language: {
                search: 'Search upcoming events:',
                lengthMenu: 'Display _MENU_ events per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ events'
            },
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    }

    var pastTable = document.getElementById('pastEventsTable');
    if (pastTable) {
        $('#pastEventsTable').DataTable({
            language: {
                search: 'Search previous events:',
                lengthMenu: 'Display _MENU_ events per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ events'
            }
        });
    }

    $(document).on('click', '.btn-register-event', function () {
        var button = $(this);
        var modal = $('#eventRegisterModal');

        var eventId = button.data('event-id');
        var title = button.data('event-title') || '';
        var location = button.data('event-location') || '';
        var speakers = button.data('event-speakers') || '';
        var fee = button.data('event-fee');
        var feeCurrency = button.data('event-fee-currency') || 'PKR';
        var bankName = button.data('event-bank-name') || '';
        var bankAccount = button.data('event-bank-account') || '';

        $('#eventRegisterForm').attr('action', '/events/' + eventId + '/register');
        $('#eventRegisterTitle').text(title);

        var metaParts = [];
        if (location) metaParts.push('Location: ' + location);
        if (speakers) metaParts.push('Speakers: ' + speakers);
        $('#eventRegisterMeta').text(metaParts.join(' | '));

        var hasFee = fee !== null && fee !== '' && !isNaN(fee);
        var paymentSection = $('#eventPaymentSection');
        if (hasFee) {
            $('#eventFeeText').text('Fee: ' + feeCurrency + ' ' + parseFloat(fee).toFixed(2));
            $('#eventBankName').text(bankName || '-');
            $('#eventBankAccount').text(bankAccount || '-');
            paymentSection.show();
        } else {
            paymentSection.hide();
        }

        modal.modal('show');
    });
});
</script>
@else
<script src="{{ asset('js/index.global.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/api/events',
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        eventClick: function(info) {
            if (confirm('Delete this event?')) {
                fetch(`/events/${info.event.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        info.event.remove();
                    }
                });
            }
        }
    });

    calendar.render();

    document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        fetch('/events', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                document.getElementById('eventForm').reset();
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
    });

    document.querySelector('input[type="color"]').addEventListener('change', function() {
        document.querySelector('input[type="text"]').value = this.value;
    });
});
</script>
@endif
@endpush
