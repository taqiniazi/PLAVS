@extends('layouts.dashboard')

@section('title', 'PLAVS  - Dashboard')

@section('content')
@php
$user = auth()->user();
$isPublic = $user->isPublic();
$hasAdminRole = $user->hasAdminRole();
@endphp

<div class="row">
    {{-- Admin/Librarian/Owner Stats --}}
    @if($hasAdminRole)
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

    @if($user->isOwner() && (($stats['total_libraries'] ?? 0) == 0))
    <div class="col-12 mb-3">
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-semibold">You have no libraries yet.</span>
                <span class="ms-1">Create your library and upload its logo to start managing books.</span>
            </div>
            <a href="{{ route('libraries.create') }}" class="btn btn-sm btn-primary">
                Create Library
            </a>
        </div>
    </div>
    @endif

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
                <small class="text-muted d-block mb-1">Wishlist</small>
                <h4 class="fw-bold mb-0">{{ $stats['wishlist_count'] ?? 0 }}</h4>
            </div>
            <div class="stat-icon">
                <i class="fas fa-heart fa-2x text-danger"></i>
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
                            <td>{{ $library->rooms_count }}</td>
                            <td>{{ $library->shelves_count }}</td>
                            <td>{{ $library->books_count }}</td>
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
                <a href="{{ route('books.show', $book) }}" class="book-card text-decoration-none">
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="img-fluid">
                    <p class="book-title">{{ strlen($book->title) > 18 ? substr($book->title, 0, 15) . '...' : $book->title }}</p>
                    <span class="book-author">Author : {{ $book->author }}</span>
                </a>
            </div>
            @endforeach
        </div>

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
                    <a href="{{ route('libraries.other') }}" class="btn btn-primary">
                        <i class="fas fa-search"></i> Browse Library
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        @else
        {{-- Default: Recently Added Books --}}
        <div class="col-lg-9">
            <h6 class="fw-bold mb-3">Recently Added</h6>
            <div class="row">
                @foreach($recent_books as $book)
                <div class="col-md-3 col-6 mb-3">
                    <a href="{{ route('books.show', $book) }}" class="book-card text-decoration-none">
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="img-fluid">
                        <p class="book-title">{{ strlen($book->title) > 18 ? substr($book->title, 0, 15) . '...' : $book->title }}</p>
                        <span class="book-author">Author : {{ $book->author }}</span>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
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
                <form method="POST" action="{{ route('permissions.request-owner') }}" enctype="multipart/form-data">
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

                            <div class="col-12 mt-4">
                                <h6 class="fw-bold border-bottom pb-2">Payment Details (Fee: 1000 PKR)
                                    <br><small class="text-muted fw-normal">Please select a payment method to view account details, and upload a screenshot of the payment.</small>
                                </h6>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label d-block mb-2">Payment Method</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pm_easypaisa" value="easypaisa" required {{ old('payment_method') == 'easypaisa' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pm_easypaisa">Easypaisa</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pm_jazzcash" value="jazzcash" required {{ old('payment_method') == 'jazzcash' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pm_jazzcash">Jazzcash</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pm_banktransfer" value="banktransfer" required {{ old('payment_method') == 'banktransfer' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pm_banktransfer">Bank Transfer</label>
                                </div>
                            </div>

                            <div class="col-md-12 mt-2 d-none" id="payment-info-container">
                                <div class="p-3 bg-light rounded border border-secondary border-opacity-25">
                                    <h6 class="fw-bold text-primary mb-2">Transfer Details</h6>
                                    <p class="mb-1"><strong>Account Title:</strong> abc</p>
                                    <p class="mb-0"><strong id="payment-account-label">Account No.</strong>: 030000000000</p>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Transaction Screenshot</label>
                                <input type="file" name="transaction_screenshot" class="form-control" accept="image/*" required>
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
        document.addEventListener('DOMContentLoaded', function() {
            var mapping = @json(config('countries'));
            var $countrySelect = $('#owner_request_country');
            var $citySelect = $('#owner_request_city');

            if ($countrySelect.length === 0 || $citySelect.length === 0) {
                return;
            }

            function populateOwnerRequestCities() {
                var selectedCountry = $countrySelect.val();
                var cities = mapping[selectedCountry] || [];
                
                // Clear current options
                $citySelect.empty();
                
                // Add default option
                $citySelect.append(new Option('Select city', '', true, true));
                
                // Add city options
                cities.forEach(function(city) {
                    $citySelect.append(new Option(city, city, false, false));
                });

                // Trigger change event to update Select2
                $citySelect.trigger('change');
            }

            // Listen for change on country select using jQuery to catch Select2 changes
            $countrySelect.on('change', function() {
                // We don't want to clear value immediately, let populate handle it
                // But we do want to reset city selection when country changes
                populateOwnerRequestCities();
            });

            // Initial population if country is already selected (e.g. old input)
            if ($countrySelect.val()) {
                var oldCity = "{{ old('library_city') }}";
                populateOwnerRequestCities();
                if (oldCity) {
                    $citySelect.val(oldCity).trigger('change');
                }
            }

            // Payment Method Logic
            var $paymentRadios = $('input[name="payment_method"]');
            var $paymentInfoContainer = $('#payment-info-container');
            var $paymentAccountLabel = $('#payment-account-label');

            function updatePaymentInfo() {
                var selectedMethod = $('input[name="payment_method"]:checked').val();
                
                if (selectedMethod) {
                    $paymentInfoContainer.removeClass('d-none');
                    var labelText = 'Account No.';
                    
                    if (selectedMethod === 'jazzcash') {
                        labelText = 'Jazz Cash No.';
                    } else if (selectedMethod === 'easypaisa') {
                        labelText = 'Easypaisa No.';
                    } else if (selectedMethod === 'banktransfer') {
                        labelText = 'Rast (IBAN) No.';
                    }
                    
                    $paymentAccountLabel.text(labelText);
                } else {
                    $paymentInfoContainer.addClass('d-none');
                }
            }

            $paymentRadios.on('change', updatePaymentInfo);
            
            // Check on load (for validation errors)
            if ($('input[name="payment_method"]:checked').length > 0) {
                updatePaymentInfo();
            }
        });
    </script>
    @endsection
