@extends('layouts.dashboard')

@section('title', 'My Assigned Books')

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header">
            <h4 class="page-title">My Assigned Books</h4>
        </div>
        
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
        
        @if($assignedBooks->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Assigned Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <!-- <th style="width: 150px;">Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignedBooks as $book)
                    @php
                        // Safely access pivot (may be null for directly assigned books)
                        $pivot = $book->pivot ?? null;
                        $isReturned = optional($pivot)->is_returned ?? false;

                        // Determine Assigned Date: prefer pivot assigned_at, fallback to controller-provided assigned_date, then N/A
                        $assignedAtValue = optional($pivot)->assigned_at ?? ($book->assigned_date ?? null);
                        if ($assignedAtValue instanceof \Carbon\Carbon) {
                            $assignedAt = $assignedAtValue->copy()->setTimezone(date_default_timezone_get())->format('M d, Y');
                        } elseif ($assignedAtValue) {
                            $assignedAt = \Carbon\Carbon::parse($assignedAtValue)->setTimezone(date_default_timezone_get())->format('M d, Y');
                        } else {
                            $assignedAt = 'N/A';
                        }
                        // Assigned time (12-hour format, machine local)
                        $assignedTime = $assignedAtValue ? (($assignedAtValue instanceof \Carbon\Carbon)
                            ? $assignedAtValue->copy()->setTimezone(date_default_timezone_get())->format('h:i A')
                            : \Carbon\Carbon::parse($assignedAtValue)->setTimezone(date_default_timezone_get())->format('h:i A'))
                            : '';

                        // Determine Return Date from pivot if available
                        $returnDateValue = optional($pivot)->return_date;
                        if ($returnDateValue instanceof \Carbon\Carbon) {
                            $returnDate = $returnDateValue->copy()->setTimezone(date_default_timezone_get())->format('M d, Y');
                        } elseif ($returnDateValue) {
                            $returnDate = \Carbon\Carbon::parse($returnDateValue)->setTimezone(date_default_timezone_get())->format('M d, Y');
                        } else {
                            $returnDate = '-';
                        }
                        // Return time (12-hour format, machine local)
                        $returnTime = $returnDateValue ? (($returnDateValue instanceof \Carbon\Carbon)
                            ? $returnDateValue->copy()->setTimezone(date_default_timezone_get())->format('h:i A')
                            : \Carbon\Carbon::parse($returnDateValue)->setTimezone(date_default_timezone_get())->format('h:i A'))
                            : '';
                    @endphp
                    <tr class="{{ $isReturned ? 'table-secondary' : '' }}" style="{{ $isReturned ? 'opacity: 0.7;' : '' }}">
                        <td>
                            <img src="{{ $book->cover_url }}"
                                 alt="{{ $book->title }}"
                                 class="img-thumbnail"
                                 style="width: 60px; height: 80px; object-fit: cover;">
                        </td>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="text-decoration-none {{ $isReturned ? 'text-muted' : 'text-dark' }}">
                                {{ $book->title }}
                            </a>
                        </td>
                        <td>{{ $book->author }}</td>
                        <td>{{ $assignedAt }} <div><small>{{ $assignedTime }}</small></div></td>
                        <td>{{ $returnDate }} <div><small>{{ $returnTime }}</small></div></td>
                        <td>
                            @if($isReturned)
                            <span class="badge bg-secondary">Returned</span>
                            @else
                            <span class="badge bg-success">In Use</span>
                            @endif
                        </td>
                        <!-- <td>
                            {{-- No return action for students per requirement --}}
                            <span class="text-muted">-</span>
                        </td> -->
                    </tr>
                    @endforeach

                    {{-- Returned books will be rendered in the combined table below; removed inline duplication --}}
                </tbody>
            </table>
        </div>

        @else
        <div class="text-center py-5">
            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No books have been assigned to you yet.</h5>
            <p class="text-muted">Check back later or contact your teacher/librarian.</p>
        </div>
        @endif
    </div>
</div>

@php
    $returnHistory = auth()->user()->booksThroughAssignment()
        ->wherePivot('is_returned', true)
        ->get()
        ->sortByDesc(function($b){
            $d = optional($b->pivot)->return_date;
            return $d ? \Carbon\Carbon::parse($d) : $b->updated_at;
        });
    $hasAnyRows = ($assignedBooks->count() > 0) || ($returnHistory->count() > 0);
@endphp
@if($hasAnyRows)
<div class="row mt-2">
    <div class="col-lg-12">
        <div class="table-responsive">
            <h5 class="mb-3">Return History</h5>
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Assigned Date</th>
                        <th>Returned On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returnHistory as $book)
                    @php
                        $pivot = $book->pivot ?? null;
                    
                        // Assigned date/time
                        $assignedAtValue = optional($pivot)->assigned_at ?? null;
                        if ($assignedAtValue instanceof \Carbon\Carbon) {
                            $assignedAt = $assignedAtValue->copy()->setTimezone(date_default_timezone_get())->format('M d, Y');
                            $assignedTime = $assignedAtValue->copy()->setTimezone(date_default_timezone_get())->format('h:i A');
                        } elseif ($assignedAtValue) {
                            $assignedAt = \Carbon\Carbon::parse($assignedAtValue)->setTimezone(date_default_timezone_get())->format('M d, Y');
                            $assignedTime = \Carbon\Carbon::parse($assignedAtValue)->setTimezone(date_default_timezone_get())->format('h:i A');
                        } else {
                            $assignedAt = 'N/A';
                            $assignedTime = '';
                        }
                    
                        // Return date/time
                        $returnDateValue = optional($pivot)->return_date ?? null;
                        if ($returnDateValue instanceof \Carbon\Carbon) {
                            $returnedOn = $returnDateValue->copy()->setTimezone(date_default_timezone_get())->format('M d, Y');
                            $returnedTime = $returnDateValue->copy()->setTimezone(date_default_timezone_get())->format('h:i A');
                        } elseif ($returnDateValue) {
                            $returnedOn = \Carbon\Carbon::parse($returnDateValue)->setTimezone(date_default_timezone_get())->format('M d, Y');
                            $returnedTime = \Carbon\Carbon::parse($returnDateValue)->setTimezone(date_default_timezone_get())->format('h:i A');
                        } else {
                            $returnedOn = '-';
                            $returnedTime = '';
                        }
                    @endphp
                        <tr class="table-secondary" style="opacity: 0.9;">
                            <td>
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="img-thumbnail" style="width: 60px; height: 80px; object-fit: cover;">
                            </td>
                            <td>
                                <a href="{{ route('books.show', $book) }}" class="text-decoration-none text-muted">{{ $book->title }}</a>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $assignedAt }} <div><small>{{ $assignedTime }}</small></div></td>
                            <td>{{ $returnedOn }} <div><small>{{ $returnedTime }}</small></div></td>
                            <td><span class="badge bg-secondary">Returned</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection


