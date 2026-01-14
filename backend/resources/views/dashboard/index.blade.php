@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Dashboard')

@section('content')
@php
    $user = auth()->user();
    $isPublic = $user->isPublic();
    $hasAdminRole = $user->hasAdminRole();
@endphp

<div class="row">
    {{-- Admin/Librarian/Owner Stats --}}
    @if($hasAdminRole)
        {{-- Action Buttons --}}
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-end gap-2">
                @if($user->isOwner())
                    <a href="{{ route('libraries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Library
                    </a>
                @endif
                <a href="{{ route('invitations.create') }}" class="btn btn-success">
                    <i class="fas fa-envelope me-2"></i>Invite Member
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Total Libraries</small>
                    <h4 class="fw-bold mb-0">{{ $stats['total_libraries'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/total_books.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Total Books</small>
                    <h4 class="fw-bold mb-0">{{ $stats['total_books'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Book Shelves</small>
                    <h4 class="fw-bold mb-0">{{ $stats['book_shelves'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">{{ $user->isOwner() ? 'Total Librarians' : 'Active Members' }}</small>
                    <h4 class="fw-bold mb-0">{{ $user->isOwner() ? ($stats['total_librarians'] ?? 0) : $stats['active_members'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/users_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    @elseif($isPublic)
        {{-- Public: Request Owner Role action --}}
        @if($isPublic)
            <div class="col-12 text-end mb-3">
                @php
                    $hasPendingOwnerRequest = \App\Models\OwnerRequest::where('user_id', $user->id)->where('status', 'pending')->exists();
                @endphp
                @if(!$hasPendingOwnerRequest)
                    <button type="button" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#ownerRequestModal">
                        <i class="fas fa-user-tie"></i> Request Owner Role
                    </button>
                    <!-- <small class="text-muted ms-2">If you own a library, request Owner access to create and manage it.</small> -->
                @else
                    <div class="alert alert-info text-center" role="alert">
                        <i class="fas fa-hourglass-half"></i> Your request for Owner role is pending approval.
                    </div>
                @endif
            </div>
        @endif

        {{-- Student Stats --}}
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">My Assigned Books</small>
                    <h4 class="fw-bold mb-0">{{ $stats['my_assigned_books'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/total_books.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Books Read</small>
                    <h4 class="fw-bold mb-0">{{ $stats['books_read'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_borrowed_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Currently Reading</small>
                    <h4 class="fw-bold mb-0">{{ $stats['currently_reading'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Teachers</small>
                    <h4 class="fw-bold mb-0">{{ $stats['my_teachers'] ?? 0 }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/users_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    @else
        {{-- Default Stats for other roles --}}
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Total Books</small>
                    <h4 class="fw-bold mb-0">{{ $stats['total_books'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/total_books.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Active Members</small>
                    <h4 class="fw-bold mb-0">{{ $stats['active_members'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/users_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Books Borrowed</small>
                    <h4 class="fw-bold mb-0">{{ $stats['books_borrowed'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_borrowed_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div>
                    <small class="text-muted d-block mb-1">Book Shelves</small>
                    <h4 class="fw-bold mb-0">{{ $stats['book_shelves'] }}</h4>
                </div>
                <div class="stat-icon">
                    <img src="{{ asset('images/book_shelves_icon.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    @endif
</div>

<div class="row mt-3">
    {{-- Show different content based on role --}}
    
    {{-- Admin/Librarian/Owner: Libraries and Recently Added --}}
    @if($hasAdminRole)
        <div class="col-lg-9">
            @if(auth()->user()->hasRole('Super Admin'))
                <h6 class="fw-bold mb-3">Libraries Overview</h6>
                <div class="table-card mb-3">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Library Name</th>
                                    <th>Type</th>
                                    <th>Rooms</th>
                                    <th>Shelves</th>
                                    <th>Books</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($libraries ?? [] as $library)
                                <tr>
                                    <td>
                                        <i class="fas fa-building me-2"></i>
                                        {{ $library->name }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $library->type === 'public' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($library->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $library->rooms->count() }}</td>
                                    <td>{{ $library->shelves->count() }}</td>
                                    <td>{{ $library->books->count() }}</td>
                                    <td>
                                        <a href="{{ route('libraries.show', $library) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-building fa-2x text-muted mb-2 d-block"></i>
                                        No libraries found. Create your first library!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            <h6 class="fw-bold mb-3">Recently Added Books</h6>
            <div class="row">
                @foreach($recent_books as $book)
                <div class="col-md-3 col-6 mb-3">
                    <div class="book-card">
                        <img src="{{ $book['image'] }}" alt="{{ $book['title'] }}" class="img-fluid">
                        <p class="book-title">{{ $book['title'] }}</p>
                        <span class="book-author">Author : {{ $book['author'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        {{-- Student: Assigned Books --}}
        <div class="col-lg-9">
            <h6 class="fw-bold mb-3">My Assigned Books</h6>
            <div class="row">
                @forelse($my_assigned_books ?? [] as $book)
                <div class="col-md-4 mb-3">
                    <div class="book-card h-100">
                        <img src="{{ $book->cover_url }}"
                             alt="{{ $book->title }}" class="img-fluid">
                        <p class="book-title">{{ $book->title }}</p>
                        <span class="book-author">Author : {{ $book->author }}</span>
                        <div class="mt-2">
                            <span class="badge {{ $book->status === 'Available' ? 'bg-success' : 'bg-warning' }}">
                                {{ $book->status }}
                            </span>
                        </div>
                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No books assigned to you yet.</p>
                        <a href="{{ route('books.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Library
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    @else
        {{-- Default: Recently Added Books --}}
        <div class="col-lg-9">
            <h6 class="fw-bold mb-3">Recently Added</h6>
            <div class="row">
                @foreach($recent_books as $book)
                <div class="col-md-3 col-6 mb-3">
                    <div class="book-card">
                        <img src="{{ $book['image'] }}" alt="{{ $book['title'] }}" class="img-fluid">
                        <p class="book-title">{{ $book['title'] }}</p>
                        <span class="book-author">Author : {{ $book['author'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="col-lg-3">
        <h6 class="fw-bold mb-3">Recent Activities</h6>
        <div class="bg-white p-3 rounded-3 border-0">
            @foreach($recent_activities as $activity)
            <div class="timeline-item">
                <div class="timeline-dot active"></div>
                <div>
                    <p class="mb-0 small fw-bold">{{ $activity['type'] }}</p>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $activity['description'] }}</small>
                    <br>
                    <small class="text-muted" style="font-size: 0.7rem;">{{ $activity['time'] }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@if($isPublic)
<!-- Owner Request Modal -->
<div class="modal fade" id="ownerRequestModal" tabindex="-1" aria-labelledby="ownerRequestModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ownerRequestModalLabel">Request Owner Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('permissions.request-owner') }}">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Library Name</label>
              <input type="text" name="library_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Library Phone No.</label>
              <input type="text" name="library_phone" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Country</label>
              <select name="library_country" id="owner_request_country" class="form-select">
                <option value="">Select country</option>
                @foreach(array_keys(config('countries')) as $country)
                  <option value="{{ $country }}" {{ old('library_country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">City</label>
              <select name="library_city" id="owner_request_city" class="form-select">
                <option value="">Select city</option>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <input type="text" name="library_address" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-1"></i> Submit Request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapping = @json(config('countries'));
        var countrySelect = document.getElementById('owner_request_country');
        var citySelect = document.getElementById('owner_request_city');
        if (!countrySelect || !citySelect) {
            return;
        }
        function populateOwnerRequestCities() {
            var selectedCountry = countrySelect.value;
            var cities = mapping[selectedCountry] || [];
            citySelect.innerHTML = '';
            var emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = 'Select city';
            citySelect.appendChild(emptyOption);
            cities.forEach(function (city) {
                var opt = document.createElement('option');
                opt.value = city;
                opt.textContent = city;
                citySelect.appendChild(opt);
            });
        }
        countrySelect.addEventListener('change', function () {
            citySelect.value = '';
            populateOwnerRequestCities();
        });
        populateOwnerRequestCities();
    });
</script>
@endsection
