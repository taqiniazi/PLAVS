@extends('layouts.dashboard')

@section('title', 'MyBookShelf - Add New Book')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="page-header mb-3">
            <h4 class="page-title">Add New Book</h4>
        </div>
        <div class="form-container">
            <!-- Auto-Fill Book Details -->
            <div class="mb-3">
                <label class="form-label fw-medium">Auto-Fill Book Details</label>
                <div class="input-group">
                    <input type="text" id="smartSearchInput" class="form-control" placeholder="Enter Book Title or Google Volume ID">
                    <button type="button" id="smartSearchBtn" class="btn btn-primary">Search</button>
                </div>
                <small class="text-muted">Search Google Books by title or volume ID and autofill the fields.</small>
            </div>

            <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- QR Scan Button -->
                <div class="mb-3">
                    <button type="button" id="scanBtn" class="btn btn-outline-primary">Scan ISBN QR Code</button>
                </div>

                <!-- Reader area (hidden initially) -->
                <div id="scanArea" style="display:none; margin-bottom:1rem;">
                    <div id="reader" style="width:100%; max-width:400px; margin-bottom:0.5rem;"></div>
                    <button type="button" id="stopScanBtn" class="btn btn-sm btn-secondary">Stop Scanning</button>
                </div>

                <div class="mb-4">
                    <label class="form-label">Book Cover Image</label>
                    <div>
                        <img id="image-preview" src="" alt="Preview" style="max-width:150px; display:none; margin-bottom:8px;" />
                        <div id="cover-warning" class="alert alert-warning mt-2 d-none">No cover image available from Google Books. Please upload an image or use the scanner.</div>
                    </div>
                    <input type="file" id="cover_image" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                    <input type="hidden" name="scanned_image_url" id="scanned_image_url">
                    <small class="text-muted">Upload a cover image for the book (optional). Scanned cover will be used if no file is selected.</small>
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Book Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Enter Book Title" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Edition</label>
                        <input type="text" id="edition" name="edition" class="form-control" placeholder="Enter edition">
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">ISBN Number</label>
                        <input type="text" id="isbn" name="isbn" class="form-control" placeholder="Enter ISBN">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Publisher Name</label>
                        <input type="text" id="publisher" name="publisher" class="form-control" placeholder="Enter publisher name">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Publish Date</label>
                        <input type="date" id="publish_date" name="publish_date" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Author</label>
                        <input type="text" id="author" name="author" class="form-control" placeholder="Enter author name" required>
                        <!-- <small class="text-muted">Auto-filled from Google Books or enter manually.</small> -->
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Shelf Location</label>
                        <select id="shelf" name="shelf" class="form-select" required>
                            <option value="">Select shelf</option>
                            @forelse($shelves as $shelf)
                                <option value="{{ $shelf->name }}">{{ $shelf->name }}</option>
                            @empty
                                <option value="" disabled>No shelves found. Please create a shelf first.</option>
                            @endforelse
                        </select>
                        <!-- <small class="text-muted">Only shelves from your libraries are listed. Create shelves first in your library/room.</small> -->
                    </div>
                </div>

                {{-- Owner field removed: owner is set to the authenticated user server-side. --}}

                {{-- Removed duplicate Shelf select block. Single shelf selector is provided above in the row. --}}

                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Brief description of the book"></textarea>
                </div>

                <div class="mt-4 ms-auto">
                    <button type="submit" class="btn btn-submit">Add Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Smart Search Results Modal -->
<div class="modal fade" id="smartSearchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="smartResults" class="list-group"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
    var scanner = null;

    function stopScanner() {
        if (scanner) {
            try {
                scanner.clear().then(function(){
                    $('#reader').empty();
                }).catch(function(err){
                    console.warn('Error stopping scanner', err);
                });
            } catch(e) {
                console.warn('scanner stop error', e);
            }
            scanner = null;
        }
        $('#scanArea').hide();
    }

    function onScanSuccess(decodedText) {
        // Attempt to extract ISBN (digits and X)
        var isbn = decodedText.replace(/[^0-9Xx]/g, '');
        stopScanner();
        if (!isbn) {
            alert('Scanned code did not contain a valid ISBN.');
            return;
        }

        // Query Google Books API
        $.getJSON('https://www.googleapis.com/books/v1/volumes?q=isbn:' + encodeURIComponent(isbn))
            .done(function(data){
                if (data.totalItems && data.items && data.items.length) {
                    var info = data.items[0].volumeInfo || {};

                    $('#title').val(info.title || '');
                    if (info.authors && info.authors.length) {
                        var authorName = info.authors[0];
                        $('#author').val(authorName);
                    }
                    $('#isbn').val(isbn);
                    $('#publisher').val(info.publisher || '');
                    if (info.publishedDate) {
                        // Try to fill date input (YYYY-MM-DD or YYYY)
                        var pd = info.publishedDate;
                        if (pd.length === 4) pd = pd + '-01-01';
                        $('#publish_date').val(pd.substring(0,10));
                    }
                    $('#edition').val('');
                    $('#description').val(info.description || '');

                    // Prefer available image links (thumbnail > smallThumbnail)
                    var thumb = '';
                    if (info.imageLinks) {
                        thumb = info.imageLinks.thumbnail || info.imageLinks.smallThumbnail || '';
                    }

                    if (thumb) {
                        thumb = thumb.replace('http://', 'https://');
                        $('#image-preview').attr('src', thumb).show();
                        $('#scanned_image_url').val(thumb);
                        $('#cover-warning').addClass('d-none');
                    } else {
                        $('#image-preview').hide().attr('src', '');
                        $('#scanned_image_url').val('');
                        $('#cover-warning').removeClass('d-none').text('No cover image available from Google Books. Please upload an image or use the scanner.');
                    }
                } else {
                    alert('No book found for ISBN ' + isbn);
                }
            }).fail(function(){
                alert('Failed to fetch book details from Google Books.');
            });
    }

    function onScanError(errorMessage) {
        // ignore for now
        // console.log('Scan error', errorMessage);
    }

    $('#scanBtn').on('click', function(){
        $('#scanArea').show();
        if (!scanner) {
            scanner = new Html5Qrcode("reader");
            Html5Qrcode.getCameras().then(function(cameras){
                if (cameras && cameras.length) {
                    var cameraId = cameras[0].id;
                    scanner.start(cameraId, { fps: 10, qrbox: 250 }, onScanSuccess, onScanError).catch(function(err){
                        alert('Unable to start camera for scanning: ' + err);
                    });
                } else {
                    alert('No camera found to scan QR codes.');
                }
            }).catch(function(err){
                alert('Camera access error: ' + err);
            });
        }
    });

    $('#stopScanBtn').on('click', function(){
        stopScanner();
    });

    // Hide scan area on page load

    // Smart Fetch (Google Books) functionality
    function renderResults(items) {
        var $list = $('#smartResults').empty();
        items.forEach(function(item){
            var info = item.volumeInfo || {};
            var thumb = (info.imageLinks && info.imageLinks.thumbnail) ? info.imageLinks.thumbnail.replace('http://','https://') : '';
            var title = info.title || 'No title';
            var authors = info.authors ? info.authors.join(', ') : '';

            var $link = $('<button type="button" class="list-group-item list-group-item-action d-flex align-items-center"></button>');
            var $img = $('<img>').attr('src', thumb).css({'width':'48px','height':'64px','object-fit':'cover','margin-right':'12px'}).on('error', function(){ $(this).hide(); });
            $link.append($img).append($('<div>').html('<strong>' + $('<div>').text(title).html() + '</strong><div class="small text-muted">' + $('<div>').text(authors).html() + '</div>'));
            $link.data('volume', item);
            $link.on('click', function(){ fillFormFromVolume($(this).data('volume')); $('#smartSearchModal').modal('hide'); });
            $list.append($link);
        });
    }

    function fillFormFromVolume(item) {
        var info = item.volumeInfo || {};
        $('#title').val(info.title || '');

        if (info.authors && info.authors.length) {
            var authorName = info.authors.join(', ');
            $('#author').val(authorName);
        }

        // ISBN
        var isbn = '';
        if (info.industryIdentifiers) {
            info.industryIdentifiers.forEach(function(id){
                if (id.type === 'ISBN_13') isbn = id.identifier;
            });
            if (!isbn) {
                info.industryIdentifiers.forEach(function(id){
                    if (id.type === 'ISBN_10') isbn = id.identifier;
                });
            }
        }
        $('#isbn').val(isbn);
        $('#publisher').val(info.publisher || '');

        if (info.publishedDate) {
            var pd = info.publishedDate;
            if (pd.length === 4) pd = pd + '-01-01';
            $('#publish_date').val(pd.substring(0,10));
        }

        $('#edition').val('');
        var desc = info.description ? $('<div>').html(info.description).text() : '';
        $('#description').val(desc);

        // Prefer available image links (thumbnail > smallThumbnail)
        var thumb = '';
        if (info.imageLinks) {
            thumb = info.imageLinks.thumbnail || info.imageLinks.smallThumbnail || '';
        }

        if (thumb) {
            thumb = thumb.replace('http://', 'https://');
            $('#image-preview').attr('src', thumb).show();
            $('#scanned_image_url').val(thumb);
            $('#cover-warning').addClass('d-none');
        } else {
            // No cover provided by Google Books for this volume
            $('#image-preview').hide().attr('src', '');
            $('#scanned_image_url').val('');
            $('#cover-warning').removeClass('d-none').text('No cover image available from Google Books. Please upload an image or use the scanner.');
        }
    }

    $('#smartSearchBtn').on('click', function(){
        var query = $('#smartSearchInput').val().trim();
        if (!query) { alert('Please enter a book title or Google Volume ID to search.'); return; }

        $('#smartSearchBtn').prop('disabled', true).text('Searching...');

        function doTitleSearch(q) {
            $.getJSON('https://www.googleapis.com/books/v1/volumes?q=' + encodeURIComponent(q))
                .done(function(data){
                    if (data.totalItems && data.items && data.items.length) {
                        renderResults(data.items.slice(0,5));
                        $('#smartSearchModal').modal('show');
                    } else {
                        alert('No results found.');
                    }
                })
                .fail(function(xhr){
                    if (xhr && xhr.status === 503) {
                        alert('Google Books service is temporarily unavailable (503). Please try again later.');
                    } else {
                        alert('Error searching Google Books API.');
                    }
                })
                .always(function(){ $('#smartSearchBtn').prop('disabled', false).text('Search'); });
        }

        // Heuristic: if query contains whitespace or punctuation common in titles, treat as title search
        var looksLikeVolumeId = !/\s/.test(query) && !/[\"'\(\)\&\%]/.test(query) && query.length <= 80;

        if (looksLikeVolumeId) {
            // Try lookup by volume ID first
            var volumeUrl = 'https://www.googleapis.com/books/v1/volumes/' + encodeURIComponent(query);
            $.getJSON(volumeUrl)
                .done(function(volume){
                    if (volume && volume.id) {
                        fillFormFromVolume(volume);
                    } else {
                        // If no data returned for volume lookup, fallback to title search
                        doTitleSearch(query);
                    }
                })
                .fail(function(xhr){
                    // If service unavailable or lookup failed, fallback to title search
                    if (xhr && xhr.status === 503) {
                        // Give user friendly feedback but still try title search
                        console.warn('Volume lookup returned 503, falling back to title search');
                    }
                    doTitleSearch(query);
                })
                .always(function(){ $('#smartSearchBtn').prop('disabled', false).text('Search'); });
        } else {
            // Treat as title search/query
            doTitleSearch(query);
        }

    });

    // Hide scan area on page load
    $('#scanArea').hide();

    // Hide cover warning when user manually chooses a file
    $('#cover_image').on('change', function(){
        if (this.files && this.files.length) {
            $('#cover-warning').addClass('d-none');
        }
    });
});
</script>
@endpush

@endsection