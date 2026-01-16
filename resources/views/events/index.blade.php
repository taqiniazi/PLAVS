@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Events')

@push('styles')
@php
    $user = auth()->user();
    $isPublic = $user && $user->isPublic();
@endphp
@if($isPublic)
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@else
    <link href="{{ asset('css/fullcalendar.global.min.css') }}" rel='stylesheet' />
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
                            <th>Description</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($upcomingEvents ?? collect()) as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->start_date->format('d M Y, h:i A') }}</td>
                                <td>{{ $event->end_date->format('d M Y, h:i A') }}</td>
                                <td>{{ Str::limit($event->description, 80) }}</td>
                                <td>{{ optional($event->creator)->name ?? 'System' }}</td>
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
                            <th>Description</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($pastEvents ?? collect()) as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->start_date->format('d M Y, h:i A') }}</td>
                                <td>{{ $event->end_date->format('d M Y, h:i A') }}</td>
                                <td>{{ Str::limit($event->description, 80) }}</td>
                                <td>{{ optional($event->creator)->name ?? 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@else
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-4">
            <h4 class="page-title">Events Calendar</h4>
            <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#eventModal">
                <i class="fas fa-plus me-2"></i>Add Event
            </button>
        </div>
        <div class="bg-white p-4 rounded-3 shadow-sm">
            <div id="calendar"></div>
        </div>
    </div>
</div>

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
            }
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
