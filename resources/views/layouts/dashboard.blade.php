<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PLAVS  - Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    @stack('styles')
    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.topbar')
        @if(request()->routeIs('dashboard'))
        @include('partials.welcome-banner')
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    <div class="footer-bottom text-center">
        <p>&copy; 2026 PLAVS . All rights reserved. | Designed with <i class="fas fa-heart-fill text-danger"></i> for book lovers everywhere</p>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    var allModals = document.querySelectorAll('.modal');

    allModals.forEach(function(modal) {
        if (modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }
    });
});

$(function () {
    if ($.fn.DataTable) {
        $('.datatable').DataTable();
    }

    if (!$.fn.select2) {
        return;
    }

    $('select.form-select').each(function () {
        var $select = $(this);
        if ($select.data('select2')) {
            return;
        }

        var $modalParent = $select.closest('.modal');
        if ($modalParent.length) {
            $select.select2({
                width: '100%',
                dropdownParent: $modalParent
            });
        } else {
            $select.select2({
                width: '100%'
            });
        }
    });

    $(document).on('shown.bs.modal', '.modal', function () {
        var $modal = $(this);
        $modal.find('select.form-select').each(function () {
            var $select = $(this);
            if ($select.data('select2')) {
                return;
            }
            $select.select2({
                width: '100%',
                dropdownParent: $modal
            });
        });
    });
});
    </script>
    @stack('scripts')
</body>
</html>
