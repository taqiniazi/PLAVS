<div class="top-bar">
    <button class="btn btn-light d-lg-none me-2" onclick="document.getElementById('sidebar').classList.toggle('show')">
        <i class="fas fa-bars"></i>
    </button>

    <div class="search-box d-none d-md-flex">
        <i class="fas fa-search text-muted"></i>
        <input type="text" placeholder="Search here...">
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Notifications Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link text-muted fs-5 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="far fa-bell position-relative">
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ $unreadNotifications }}
                    </span>
                    @endif
                </i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                <li><h6 class="dropdown-header">Notifications</h6></li>
                @forelse($notifications ?? [] as $notification)
                <li>
                    <a class="dropdown-item py-2" href="#">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-{{ $notification['icon'] ?? 'info-circle' }} text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <div class="fw-bold small">{{ $notification['title'] }}</div>
                                <div class="text-muted small">{{ $notification['message'] }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ $notification['time'] }}</div>
                            </div>
                        </div>
                    </a>
                </li>
                @empty
                <li><span class="dropdown-item text-muted">No new notifications</span></li>
                @endforelse
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center small" href="#">View all notifications</a></li>
            </ul>
        </div>

        <!-- Settings Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link text-muted fs-5 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-cog"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Settings</h6></li>
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i>Update Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('profile.password') }}"><i class="fas fa-key me-2"></i>Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>System Settings</a></li>
            </ul>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-profile">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/user.png') }}" alt="Profile" class="img-fluid">
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">{{ Auth::user()->name }}</h6></li>
                <li><span class="dropdown-item-text small text-muted">{{ ucfirst(Auth::user()->role ?? 'user') }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('profile.password') }}"><i class="fas fa-key me-2"></i>Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>