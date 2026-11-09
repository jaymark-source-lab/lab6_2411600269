<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · FitTrack Pro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/fittrack-theme.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="ft-shell">
        <aside class="ft-sidebar">
            <div class="ft-brand">
                <i class="bi bi-activity"></i> <span>Dashboard</span>
            </div>
            <nav class="ft-nav">
                <a href="{{ route('dashboard') }}" class="ft-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Overview
                </a>
                <a href="#" class="ft-nav-link"><i class="bi bi-graph-up"></i> Progress</a>
                <a href="{{ route('workouts.index') }}" class="ft-nav-link {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-week"></i> Workouts
                </a>
                <a href="{{ route('products.index') }}" class="ft-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Products
                </a>
                <a href="{{ route('transactions.index') }}" class="ft-nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i> Transactions
                </a>
                <a href="#" class="ft-nav-link"><i class="bi bi-egg-fried"></i> Nutrition</a>
                <a href="#" class="ft-nav-link"><i class="bi bi-trophy"></i> Goals</a>
                <a href="#" class="ft-nav-link"><i class="bi bi-gear"></i> Settings</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="ft-logout">
                @csrf
                <button type="submit" class="ft-nav-link ft-logout-btn">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </aside>

        <div class="ft-main">
            <header class="ft-topbar">
                <div class="ft-topbar-title">@yield('title', 'Overview')</div>
                <div class="ft-topbar-right">
                    <span class="ft-date">{{ now()->format('l, F j, Y') }}</span>

                    @auth
                        @php
                            // bell data for the logged-in user
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                            $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                        @endphp

                        {{-- notification bell --}}
                        <div class="dropdown">
                            <button class="btn ft-admin-btn position-relative" type="button" data-bs-toggle="dropdown" aria-label="Notifications">
                                <i class="bi bi-bell-fill"></i>
                                @if ($unreadCount)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-0" style="width: 340px;">
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                    <strong>Notifications</strong>
                                    @if ($unreadCount)
                                        <form method="POST" action="{{ route('notifications.readAll') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Mark all as read</button>
                                        </form>
                                    @endif
                                </div>
                                @forelse ($recentNotifications as $notification)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 border-bottom {{ $notification->read_at ? 'text-muted' : 'fw-semibold' }}" style="white-space: normal;">
                                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                            {{ $notification->data['message'] ?? 'Low stock alert' }}
                                            <div class="small text-muted fw-normal">{{ $notification->created_at->diffForHumans() }}</div>
                                        </button>
                                    </form>
                                @empty
                                    <div class="px-3 py-3 text-muted small">No notifications yet.</div>
                                @endforelse
                            </div>
                        </div>
                    @endauth

                    <div class="dropdown">
                        <button class="btn ft-admin-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name ?? 'Admin' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="ft-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>
</html>