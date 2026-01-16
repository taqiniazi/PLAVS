@extends('layouts.dashboard')

@section('title', 'Add New Shelf')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-bookmark me-2"></i>
                        Add New Shelf
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Create a new shelf to organize your books</p>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('shelves.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Shelf Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required-field">Shelf Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Enter shelf name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Library -->
                            <div class="col-md-6 mb-3">
                                <label for="library_id" class="form-label required-field">Library</label>
                                @if(isset($libraries) && $libraries->count() > 1)
                                    <select class="form-select @error('library_id') is-invalid @enderror" 
                                            id="library_id" 
                                            name="library_id" 
                                            required>
                                        <option value="">Select library</option>
                                        @foreach($libraries as $library)
                                            <option value="{{ $library->id }}" {{ old('library_id') == $library->id ? 'selected' : '' }}>
                                                {{ $library->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    @php($singleLibrary = isset($libraries) ? $libraries->first() : null)
                                    @if($singleLibrary)
                                        <input type="hidden" id="library_id" name="library_id" value="{{ $singleLibrary->id }}">
                                        <div class="library-info p-2 border rounded">
                                            <h6 class="mb-1"><i class="fas fa-building me-2"></i>Library: {{ $singleLibrary->name }}</h6>
                                            <p class="mb-0 small"><i class="fas fa-map-marker-alt me-1"></i>{{ $singleLibrary->location ?? 'No location specified' }}</p>
                                        </div>
                                    @else
                                        <select class="form-select @error('library_id') is-invalid @enderror" 
                                                id="library_id" 
                                                name="library_id" 
                                                required>
                                            <option value="">Select library</option>
                                        </select>
                                    @endif
                                @endif
                                @error('library_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Room -->
                            <div class="col-md-6 mb-3">
                                <label for="room_id" class="form-label required-field">Room</label>
                                <select class="form-select @error('room_id') is-invalid @enderror" 
                                        id="room_id" 
                                        name="room_id"
                                        required>
                                    <option value="">Select room</option>
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Shelf Code -->
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Shelf Code</label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}" 
                                       placeholder="e.g., A-01, B-02">
                                <div class="form-text">Optional: Add a unique code for easy identification</div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Describe what this shelf is for (optional)">{{ old('description') }}</textarea>
                                <div class="form-text">Optional: Add details about the shelf's purpose or contents</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="{{ route('shelves.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Create Shelf
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    var $nameInput = $('#name');
    if ($nameInput.length) {
        $nameInput.trigger('focus');
    }

    var $librarySelect = $('#library_id');
    var $roomSelect = $('#room_id');

    function renderRoomOptions(rooms, selectedRoomId) {
        var oldRoomId = selectedRoomId || '{{ old('room_id') }}';
        var hasSelect2 = !!$roomSelect.data('select2');

        if (hasSelect2) {
            $roomSelect.empty();
            $roomSelect.append(new Option('Select room', '', !oldRoomId, !oldRoomId));

            rooms.forEach(function (room) {
                var isSelected = oldRoomId && String(room.id) === String(oldRoomId);
                $roomSelect.append(new Option(room.name, room.id, isSelected, isSelected));
            });

            $roomSelect.trigger('change.select2');
        } else {
            var selectEl = $roomSelect.get(0);
            if (!selectEl) {
                return;
            }

            selectEl.innerHTML = '';
            var emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = 'Select room';
            if (!oldRoomId) {
                emptyOption.selected = true;
            }
            selectEl.appendChild(emptyOption);

            rooms.forEach(function (room) {
                var opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = room.name;
                if (oldRoomId && String(room.id) === String(oldRoomId)) {
                    opt.selected = true;
                }
                selectEl.appendChild(opt);
            });
        }
    }

    function loadRooms(libraryId, selectedRoomId) {
        renderRoomOptions([], selectedRoomId);
        if (!libraryId) {
            return;
        }

        fetch('/api/libraries/' + libraryId + '/rooms')
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                var rooms = (data && data.rooms) ? data.rooms : [];
                renderRoomOptions(rooms, selectedRoomId);
            })
            .catch(function () {
                renderRoomOptions([], null);
            });
    }

    if ($librarySelect.length && $roomSelect.length) {
        $librarySelect.on('change', function () {
            var libraryId = $(this).val();
            loadRooms(libraryId, '');
        });

        var initialLibraryId = $librarySelect.val() || '{{ old('library_id') }}';
        if (initialLibraryId) {
            loadRooms(initialLibraryId, '{{ old('room_id') }}');
        } else {
            renderRoomOptions([], '{{ old('room_id') }}');
        }
    }

    var $form = $('form').first();
    var $submitBtn = $form.find('button[type="submit"]').first();

    $form.on('submit', function () {
        if ($submitBtn.length) {
            $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');
            $submitBtn.prop('disabled', true);
        }
    });
});
</script>
@endpush
