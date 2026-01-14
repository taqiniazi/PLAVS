@extends('layouts.dashboard')

@section('title', 'Rooms')

@section('content')
@php
    $user = auth()->user();
    $isOwner = $user->isOwner();
    $isAdmin = $user->isAdmin() || $user->isSuperAdmin() || $user->isLibrarian();
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
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h4 class="page-title mb-0">Rooms</h4>
            @if($isOwner || $isAdmin)
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#selectLibraryModal">
                    <i class="fas fa-plus me-2"></i>Add Room
                </a>
            @endif
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table id="roomsTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Room Name</th>
                            <th>Library</th>
                            <th>Description</th>
                            <th class="text-end" data-orderable="false">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $room->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $room->library->name ?? '—' }}
                            </td>
                            <td>
                                {{ $room->description ?? '—' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('libraries.rooms.show', [$room->library_id, $room->id]) }}" class="btn-action btn-view" data-bs-toggle="tooltip" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(($isOwner && $room->library->owner_id === $user->id) || $isAdmin)
                                    <a href="{{ route('rooms.edit', $room) }}" class="btn-action btn-edit" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('rooms.destroy', $room) }}" style="display: inline;" onsubmit="return confirm('Delete this room?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-dispose" data-bs-toggle="tooltip" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Select Library Modal for creating room --}}
<div class="modal fade" id="selectLibraryModal" tabindex="-1" aria-labelledby="selectLibraryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selectLibraryModalLabel">Choose Library</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Select a library to add a new room.</p>
        <div class="list-group">
          @php
              if($user->isAdmin()) {
                  $modalLibraries = \App\Models\Library::all();
              } elseif($user->isOwner()) {
                  $modalLibraries = \App\Models\Library::where('owner_id', $user->id)->get();
              } elseif($user->isLibrarian()) {
                  $modalLibraries = \App\Models\Library::where('owner_id', $user->parent_owner_id)->get();
              } else {
                  $modalLibraries = collect();
              }
          @endphp
          @forelse($modalLibraries as $library)
            <a href="{{ route('libraries.rooms.create', $library) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <span>{{ $library->name }}</span>
              <i class="fas fa-angle-right"></i>
            </a>
          @empty
            <div class="text-muted">No libraries available.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush