@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Dashboard')

@section('content')
<div class="row">
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
</div>

<div class="row mt-3">
    <div class="col-lg-9">
        <h6 class="fw-bold mb-3">Recently Added</h6>
        <div class="row">
            @foreach($recent_books as $book)
            <div class="col-md-3 col-6 mb-3">
                <div class="book-card">
                    <img src="{{ asset('images/' . $book['image']) }}" alt="{{ $book['title'] }}" class="img-fluid">
                    <p class="book-title">{{ $book['title'] }}</p>
                    <span class="book-author">Author : {{ $book['author'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
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
@endsection