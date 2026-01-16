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
        <div class="page-header mb-4">
            <h4 class="page-title">{{ $isPublic ? 'All Libraries.' : 'My Libraries' }}</h4>
            @can('create', App\Models\Library::class)
            <div class="float-end">
                <a href="{{ route('libraries.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Library
                </a>
                @if(auth()->user()->isOwner())
                <a href="{{ route('librarians.create') }}" class="btn btn-success">
                    <i class="fas fa-user-plus me-2"></i>Add Librarian
                </a>
                @endif
            </div>
            @endcan
        </div>

        @if($isPublic)
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
        @endif
  <div class="table-card">
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
                                {{ $library->location ?? 'Not specified' }}
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
});
</script>
@endpush
