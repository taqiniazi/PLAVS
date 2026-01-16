@extends('layouts.dashboard')

@section('title', 'Libraries')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
@endpush

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->hasAdminRole();
    $isSuperAdmin = $user->isSuperAdmin();
    $isLibrarian = $user->isLibrarian();
    $isPublic = $user->isPublic();
    $selectedCountry = request('country');
    $selectedCity = request('city');
    $countryCityMapping = config('countries');
    $availableCities = $selectedCountry && isset($countryCityMapping[$selectedCountry])
        ? $countryCityMapping[$selectedCountry]
        : [];
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
            <h4 class="page-title mb-0">{{ $isPublic ? 'All Libraries.' : 'My Libraries' }}</h4>
            <div class="d-flex gap-2">
                @can('create', App\Models\Room::class)
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fas fa-door-open me-2"></i>Add Room
                </button>
                @endcan
                @can('create', App\Models\Shelf::class)
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addShelfModal">
                    <i class="fas fa-book me-2"></i>Add Shelf
                </button>
                @endcan
                @can('create', App\Models\Library::class)
                <a href="{{ route('libraries.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Library
                </a>
                @if(auth()->user()->isOwner())
                <a href="{{ route('librarians.create') }}" class="btn btn-success">
                    <i class="fas fa-user-plus me-2"></i>Add Librarian
                </a>
                @endif
                @endcan
            </div>
        </div>

        <!-- @if($isPublic)
        <form method="GET" action="{{ route('libraries.index') }}" class="row mb-3 g-2 align-items-end">
            <div class="col-md-4">
                <label for="filter_country" class="form-label">Country</label>
                <select id="filter_country" name="country" class="form-select">
                    <option value="">All countries</option>
                    @foreach(($countryCityMapping ?? []) as $country => $cities)
                        <option value="{{ $country }}"
                                data-cities="{{ implode('|', $cities) }}"
                                {{ $selectedCountry === $country ? 'selected' : '' }}>
                            {{ $country }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_city" class="form-label">City</label>
                <select id="filter_city" name="city" class="form-select" data-selected-city="{{ $selectedCity }}">
                    <option value="">All cities</option>
                    @foreach($availableCities as $city)
                        <option value="{{ $city }}" {{ $selectedCity === $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if($selectedCountry || $selectedCity)
                    <a href="{{ route('libraries.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                @endif
            </div>
        </form>
        @endif -->
  <div class="table-card mb-4">
            <div class="table-responsive">
                <table id="librariesTable" class="table table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Library Name</th>
                            <th>Location</th>
                            <th>Map</th>
                            <th>Owner Name</th>
                            <th>Total Books</th>
                            <th class="text-end" data-orderable="false">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($libraries as $library)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $library->name }}</h6>
                                        @if($library->type)
                                        <small class="text-muted">
                                            <span class="badge {{ $library->type === 'public' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($library->type) }}
                                            </span>
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $rawLocation = $library->location;
                                    $address = null;
                                    $city = null;
                                    $country = null;

                                    if ($rawLocation) {
                                        $parts = array_map('trim', explode(',', $rawLocation));

                                        if (count($parts) === 1) {
                                            $address = $parts[0];
                                        } elseif (count($parts) === 2) {
                                            $address = $parts[0];
                                            $city = $parts[1];
                                        } elseif (count($parts) >= 3) {
                                            $address = implode(', ', array_slice($parts, 0, -2));
                                            $city = $parts[count($parts) - 2] ?? null;
                                            $country = $parts[count($parts) - 1] ?? null;
                                        }
                                    }
                                @endphp

                                @if(!$rawLocation)
                                    Not specified
                                @else
                                    @if($address)
                                        <div>{{ $address }}</div>
                                    @endif
                                    @if($city || $country)
                                        <div class="text-muted small">
                                            @if($city)
                                                {{ $city }}
                                            @endif
                                            @if($city && $country)
                                                ,
                                            @endif
                                            @if($country)
                                                {{ $country }}
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($library->location)
                                <a href="https://maps.google.com/?q={{ urlencode($library->location) }}"
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-map-marker"></i> View Map
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{ $library->owner ? $library->owner->name : 'System' }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $library->books_count }}</span>
                            </td>
                            <td class="text-end">
                                @can('view', $library)
                                <a href="{{ route('libraries.show', $library) }}" class="btn-action btn-view" data-bs-toggle="tooltip" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('update', $library)
                                <a href="{{ route('libraries.edit', $library) }}" class="btn-action btn-edit" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('delete', $library)
                                <form method="POST" action="{{ route('libraries.destroy', $library) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this library? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-dispose" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(auth()->user()->isOwner())
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>My Librarians</h5>
            </div>
            <div class="card-body p-0">
                @if(isset($librarians) && $librarians->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($librarians as $librarian)
                            <tr>
                                <td>{{ $librarian->name }}</td>
                                <td>{{ $librarian->email }}</td>
                                <td>{{ $librarian->phone ?? '-' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('librarians.destroy', $librarian) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this librarian?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-user-minus me-1"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-3 text-muted">No librarians yet. Use "Add Librarian" to create one.</div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@can('create', App\Models\Room::class)
@php
    $canSelectLibraryForRoom = isset($libraries) && $libraries->count() > 0;
@endphp
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoomModalLabel">
                    <i class="fas fa-door-open me-2"></i>
                    Add Room
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if($canSelectLibraryForRoom)
                <form id="addRoomForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_library_id" class="form-label required-field">Library</label>
                            <select id="modal_library_id" name="library_id" class="form-select">
                                <option value="">Select library</option>
                                @foreach($libraries as $lib)
                                    <option value="{{ $lib->id }}">
                                        {{ $lib->name }}@if($lib->location) ({{ $lib->location }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please select a library.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_room_name" class="form-label required-field">Room Name</label>
                            <input type="text"
                                   id="modal_room_name"
                                   name="name"
                                   class="form-control"
                                   placeholder="Enter room name"
                                   required>
                            <div class="invalid-feedback">
                                Please enter a room name.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_room_description" class="form-label">Description</label>
                            <textarea id="modal_room_description"
                                      name="description"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Describe this room (optional)"></textarea>
                            <div class="form-text">
                                Optional: Add details about the room's purpose or features.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitAddRoom">
                            <i class="fas fa-plus me-2"></i>Create Room
                        </button>
                    </div>
                </form>
            @else
                <div class="modal-body">
                    <p class="mb-0 text-muted">
                        There are no libraries available for you to create rooms in.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            @endif
        </div>
    </div>
</div>
@endcan

@can('create', App\Models\Shelf::class)
@php
    $canSelectLibraryForShelf = isset($libraries) && $libraries->count() > 0;
@endphp
<div class="modal fade" id="addShelfModal" tabindex="-1" aria-labelledby="addShelfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addShelfModalLabel">
                    <i class="fas fa-book me-2"></i>
                    Add Shelf
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if($canSelectLibraryForShelf)
                <form id="addShelfForm" method="POST" action="{{ route('shelves.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_shelf_name" class="form-label required-field">Shelf Name</label>
                            <input type="text"
                                   id="modal_shelf_name"
                                   name="name"
                                   class="form-control"
                                   placeholder="Enter shelf name"
                                   required>
                            <div class="invalid-feedback">
                                Please enter a shelf name.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_shelf_library_id" class="form-label required-field">Library</label>
                            <select id="modal_shelf_library_id" name="library_id" class="form-select" required>
                                <option value="">Select library</option>
                                @foreach($libraries as $lib)
                                    <option value="{{ $lib->id }}">
                                        {{ $lib->name }}@if($lib->location) ({{ $lib->location }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please select a library.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_shelf_room_id" class="form-label required-field">Room</label>
                            <select id="modal_shelf_room_id" name="room_id" class="form-select" required>
                                <option value="">Select room</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a room.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_shelf_code" class="form-label">Shelf Code</label>
                            <input type="text"
                                   id="modal_shelf_code"
                                   name="code"
                                   class="form-control"
                                   placeholder="e.g., A-01, B-02">
                            <div class="form-text">
                                Optional: Add a unique code for easy identification. Leave blank to auto-generate.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_shelf_description" class="form-label">Description</label>
                            <textarea id="modal_shelf_description"
                                      name="description"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Describe what this shelf is for (optional)"></textarea>
                            <div class="form-text">
                                Optional: Add details about the shelf's purpose or contents.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitAddShelf">
                            <i class="fas fa-plus me-2"></i>Create Shelf
                        </button>
                    </div>
                </form>
            @else
                <div class="modal-body">
                    <p class="mb-0 text-muted">
                        There are no libraries available for you to create shelves in.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            @endif
        </div>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>

<script>
$(document).ready(function () {
    // Initialize DataTables
    var table = $('#librariesTable').DataTable({
        "language": {
            "search": "Search libraries:",
            "lengthMenu": "Display _MENU_ libraries per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ libraries"
        },
        "columnDefs": [
            { "orderable": false, "targets": [2, 5] }
        ]
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    var countrySelect = document.getElementById('filter_country');
    var citySelect = document.getElementById('filter_city');

    function getCitiesForSelectedCountry() {
        if (!countrySelect) {
            return [];
        }
        var selectedOption = countrySelect.options[countrySelect.selectedIndex];
        if (!selectedOption) {
            return [];
        }
        var raw = selectedOption.getAttribute('data-cities') || '';
        if (!raw) {
            return [];
        }
        return raw.split('|').filter(function (city) {
            return city && city.trim().length > 0;
        });
    }

    if (countrySelect && citySelect) {
        var $citySelect = $(citySelect);

        function populateCities() {
            var cities = getCitiesForSelectedCountry();
            var previous = citySelect.getAttribute('data-selected-city') || '';

            if ($citySelect.data('select2')) {
                $citySelect.empty();
                $citySelect.append(new Option('All cities', '', previous === '', previous === ''));

                cities.forEach(function (city) {
                    var isSelected = city === previous;
                    $citySelect.append(new Option(city, city, isSelected, isSelected));
                });

                $citySelect.trigger('change.select2');
            } else {
                citySelect.innerHTML = '';

                var emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = 'All cities';
                citySelect.appendChild(emptyOption);

                cities.forEach(function (city) {
                    var opt = document.createElement('option');
                    opt.value = city;
                    opt.textContent = city;
                    if (city === previous) {
                        opt.selected = true;
                    }
                    citySelect.appendChild(opt);
                });
            }
        }

        countrySelect.addEventListener('change', function () {
            citySelect.setAttribute('data-selected-city', '');
            populateCities();
        });

        populateCities();
    }

    var addRoomModal = document.getElementById('addRoomModal');
    if (addRoomModal) {
        var addRoomForm = document.getElementById('addRoomForm');
        var modalLibrarySelect = document.getElementById('modal_library_id');
        var roomNameInput = document.getElementById('modal_room_name');
        var submitAddRoomBtn = document.getElementById('submitAddRoom');

        if (modalLibrarySelect) {
            modalLibrarySelect.addEventListener('change', function () {
                modalLibrarySelect.classList.remove('is-invalid');
            });
        }

        if (roomNameInput) {
            roomNameInput.addEventListener('input', function () {
                roomNameInput.classList.remove('is-invalid');
            });
        }

        if (addRoomForm) {
            addRoomForm.addEventListener('submit', function (e) {
                var libraryId = modalLibrarySelect ? modalLibrarySelect.value : '';
                var roomName = roomNameInput ? roomNameInput.value.trim() : '';
                var hasError = false;

                if (!libraryId) {
                    if (modalLibrarySelect) {
                        modalLibrarySelect.classList.add('is-invalid');
                    }
                    hasError = true;
                }

                if (!roomName) {
                    if (roomNameInput) {
                        roomNameInput.classList.add('is-invalid');
                    }
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    return;
                }

                var baseUrl = '{{ url('/libraries') }}';
                addRoomForm.action = baseUrl + '/' + libraryId + '/rooms';

                if (submitAddRoomBtn) {
                    submitAddRoomBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
                    submitAddRoomBtn.disabled = true;
                }
            });
        }
    }

    var addShelfModal = document.getElementById('addShelfModal');
    if (addShelfModal) {
        var addShelfForm = document.getElementById('addShelfForm');
        var shelfNameInput = document.getElementById('modal_shelf_name');
        var shelfLibrarySelect = document.getElementById('modal_shelf_library_id');
        var shelfRoomSelect = document.getElementById('modal_shelf_room_id');
        var submitAddShelfBtn = document.getElementById('submitAddShelf');

        function renderShelfRoomOptions(rooms) {
            if (!shelfRoomSelect) {
                return;
            }
            shelfRoomSelect.innerHTML = '';
            var emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = 'Select room';
            shelfRoomSelect.appendChild(emptyOption);

            rooms.forEach(function (room) {
                var opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = room.name;
                shelfRoomSelect.appendChild(opt);
            });
        }

        function loadShelfRooms(libraryId) {
            renderShelfRoomOptions([]);
            if (!libraryId) {
                return;
            }

            fetch('/api/libraries/' + libraryId + '/rooms')
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    var rooms = (data && data.rooms) ? data.rooms : [];
                    renderShelfRoomOptions(rooms);
                })
                .catch(function () {
                    renderShelfRoomOptions([]);
                });
        }

        if (shelfLibrarySelect) {
            shelfLibrarySelect.addEventListener('change', function () {
                shelfLibrarySelect.classList.remove('is-invalid');
                loadShelfRooms(this.value);
            });
        }

        if (shelfRoomSelect) {
            shelfRoomSelect.addEventListener('change', function () {
                shelfRoomSelect.classList.remove('is-invalid');
            });
        }

        if (shelfNameInput) {
            shelfNameInput.addEventListener('input', function () {
                shelfNameInput.classList.remove('is-invalid');
            });
        }

        if (addShelfForm) {
            addShelfForm.addEventListener('submit', function (e) {
                var hasError = false;
                var libraryId = shelfLibrarySelect ? shelfLibrarySelect.value : '';
                var roomId = shelfRoomSelect ? shelfRoomSelect.value : '';
                var shelfName = shelfNameInput ? shelfNameInput.value.trim() : '';

                if (!shelfName) {
                    if (shelfNameInput) {
                        shelfNameInput.classList.add('is-invalid');
                    }
                    hasError = true;
                }

                if (!libraryId) {
                    if (shelfLibrarySelect) {
                        shelfLibrarySelect.classList.add('is-invalid');
                    }
                    hasError = true;
                }

                if (!roomId) {
                    if (shelfRoomSelect) {
                        shelfRoomSelect.classList.add('is-invalid');
                    }
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    return;
                }

                if (submitAddShelfBtn) {
                    submitAddShelfBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
                    submitAddShelfBtn.disabled = true;
                }
            });
        }
    }
}); 
</script>
@endpush
