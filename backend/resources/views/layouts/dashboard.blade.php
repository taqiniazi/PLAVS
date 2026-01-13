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
    @stack('styles')
    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.topbar')
        @include('partials.welcome-banner')
        
        @yield('content')
    </div>
    

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- QR code scanner library -->
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    <script>
        $(document).ready(fucntion(){
            $(".datatable").datable();
        })
    </script>
    @stack('scripts')
</body>
</html>