@extends('layouts.dashboard')

@section('title', 'My Wishlist')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}">
<style>
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
        border: 1px dashed #dee2e6;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .rating-stars {
        color: #ffc107;
        font-size: 1.1rem;
    }
    
    .rating-count {
        color: #6c757d;
        font-size: 0.85rem;
        margin-left: 5px;
    }
</style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h4 class="page-title">
                    <i class="fas fa-heart me-2"></i>
                    My Wishlist
                </h4>
                <p class="text-muted mb-0">Books you've saved for later</p>
            </div>

            @if($wishlistItems->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-heart-broken"></i>
                    <h5 class="text-muted">Your wishlist is empty</h5>
                    <p class="text-muted">Start adding books to your wishlist by visiting the books section.</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary">
                        <i class="fas fa-book me-2"></i> Browse Books
                    </a>
                </div>
            @else
                <div class="table-card">
                    <div class="table-responsive">
                        <table id="wishlistTable" class="table table-hover align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="80">Cover</th>
                                    <th>Book Details</th>
                                    <th>Status & Rating</th>
                                    <th>Library</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wishlistItems as $wishlistItem)
                                    @php $book = $wishlistItem->book; @endphp
                                    @if($book)
                                        <tr>
                                            <td>
                                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" style="width:60px;height:90px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6;">
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ Str::limit($book->title, 60) }}</div>
                                                <div class="text-muted small">Author: {{ Str::limit($book->author, 40) }}</div>
                                                <div class="text-muted small">ISBN: {{ $book->isbn ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="badge bg-secondary">{{ $book->status }}</span>
                                                </div>
                                                @if($book->average_rating > 0)
                                                    <div class="d-flex align-items-center mt-1">
                                                        <div class="rating-stars">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= round($book->average_rating))
                                                                    <i class="fas fa-star"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <span class="rating-count">
                                                            ({{ $book->rating_count }})
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $library = optional(optional(optional($book->shelf)->room)->library);
                                                @endphp
                                                @if($library && $library->name)
                                                    <div class="fw-semibold small">{{ $library->name }}</div>
                                                    @if($library->location)
                                                        <div class="text-muted small">{{ $library->location }}</div>
                                                    @endif
                                                @else
                                                    <span class="text-muted small">Not Assigned</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('books.show', $book) }}" class="btn btn-outline-primary btn-sm me-2">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                <form method="POST" action="{{ route('wishlist.toggle', $book) }}" class="d-inline wishlist-toggle-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i> Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tableElement = document.getElementById('wishlistTable');
    var dataTable = null;
    if (tableElement) {
        dataTable = $('#wishlistTable').DataTable({
            language: {
                search: 'Search Wishlist:',
                lengthMenu: 'Display _MENU_ books per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ books'
            },
            columnDefs: [
                { orderable: false, targets: [0, 4] }
            ]
        });
    }

    var wishlistForms = document.querySelectorAll('.wishlist-toggle-form');
    wishlistForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var button = form.querySelector('button[type="submit"]');
            var originalText = button.innerHTML;

            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Removing...';
            button.disabled = true;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        var row = form.closest('tr');
                        if (dataTable && row) {
                            dataTable.row($(row)).remove().draw();
                            if (dataTable.rows().count() === 0) {
                                showEmptyState();
                            }
                        } else if (row) {
                            row.remove();
                            if (!document.querySelectorAll('#wishlistTable tbody tr').length) {
                                showEmptyState();
                            }
                        }

                        showToast(data.message, 'success');
                    }
                })
                .catch(function() {
                    showToast('An error occurred. Please try again.', 'error');
                })
                .finally(function() {
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        });
    });

    function showToast(message, type) {
        var toast = document.createElement('div');
        toast.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(toast);

        setTimeout(function() {
            if (toast && toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }

    function showEmptyState() {
        var container = document.querySelector('.col-12');
        if (!container) {
            return;
        }
        container.innerHTML = '<div class="empty-state">' +
            '<i class="fas fa-heart-broken"></i>' +
            '<h5 class="text-muted">Your wishlist is empty</h5>' +
            '<p class="text-muted">Start adding books to your wishlist by visiting the books section.</p>' +
            '<a href="{{ route('books.index') }}" class="btn btn-primary">' +
            '<i class="fas fa-book me-2"></i> Browse Books' +
            '</a>' +
            '</div>';
    }
});
</script>
@endpush
