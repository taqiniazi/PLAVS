<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyBookShelf - Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    @stack('styles')
    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.topbar')
        @if(request()->routeIs('dashboard'))
        @include('partials.welcome-banner')
        @endif
        
        @yield('content')
    </div>
    

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
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
