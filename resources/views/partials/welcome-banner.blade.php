<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h5 class="mb-1">Welcome!</h5>
            <h2 class="fw-bold mb-2">{{ Auth::user()->name ?? 'User' }}</h2>
            <p class="small opacity-75 mb-0">Last Login From : {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->format('l, F j, Y, g:i A') : now()->format('l, F j, Y, g:i A') }}</p>
        </div>
        <div class="col-md-4 position-relative d-none d-md-block" style="height:100px;">
            <img src="{{ asset('images/dashboard_title_image.png') }}" class="banner-illustration" alt="Reading">
        </div>
    </div>
</div>