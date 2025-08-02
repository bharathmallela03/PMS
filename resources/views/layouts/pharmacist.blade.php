<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pharmacist Dashboard') - PharmaCare</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Base layout styles */
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .main-wrapper {
            display: flex;
            flex: 1;
        }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #0047FF; /* Vibrant blue */
            color: white;
        }
        .sidebar .nav-link {
            color: #e0e0e0;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            transition: background-color 0.2s ease-in-out;
        }
        .sidebar .nav-link:hover {
            background-color: #003bde; /* Darker shade for hover */
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #0032ba; /* Even darker for active */
            color: white;
            font-weight: bold;
        }
        .sidebar .nav-link .fa-fw {
            width: 1.5em;
        }
        .sidebar-header {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid #003bde;
        }
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
            background-color: #f8f9fa;
        }

        /* === NEW STYLES FOR USER DROPDOWN === */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e85a4f; /* A reddish color similar to the image */
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .dropdown-menu-custom {
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            padding: 0;
            overflow: hidden; /* To keep the border-radius on the header */
        }
        .dropdown-header-custom {
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #ee9ca7, #ffdde1); /* A pinkish gradient */
            color: #333;
        }
        .dropdown-header-custom h5 {
            margin-bottom: 0.25rem;
            font-weight: 600;
        }
        .dropdown-header-custom p {
            font-size: 0.875rem;
            color: #555;
            margin-bottom: 0;
        }
        .dropdown-menu-custom .dropdown-item {
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
        }
        .dropdown-menu-custom .dropdown-item .fa-fw {
            color: #6b7280;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="main-wrapper">
        <aside class="sidebar d-none d-lg-block">
            <div class="sidebar-header">
                <i class="fas fa-pills me-2"></i> PHARMACARE
            </div>
            <nav class="nav flex-column mt-3">
                <a href="{{ route('pharmacist.dashboard') }}" class="nav-link {{ request()->routeIs('pharmacist.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i> Dashboard
                </a>
                <a href="{{ route('pharmacist.medicines') }}" class="nav-link {{ request()->routeIs('pharmacist.medicines*') ? 'active' : '' }}">
                    <i class="fas fa-pills fa-fw me-2"></i> Medicines
                </a>
                <a href="{{ route('pharmacist.companies') }}" class="nav-link {{ request()->routeIs('pharmacist.companies*') ? 'active' : '' }}">
                    <i class="fas fa-building fa-fw me-2"></i> Companies
                </a>
                <a href="{{ route('pharmacist.billing') }}" class="nav-link {{ request()->routeIs('pharmacist.billing*') ? 'active' : '' }}">
                    <i class="fas fa-receipt fa-fw me-2"></i> Billing
                </a>
                <a href="{{ route('pharmacist.orders') }}" class="nav-link {{ request()->routeIs('pharmacist.orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart fa-fw me-2"></i> Orders
                </a>
                <a href="{{ route('pharmacist.reports.sales') }}" class="nav-link {{ request()->routeIs('pharmacist.reports*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line fa-fw me-2"></i> Reports
                </a>
                <a href="{{ route('pharmacist.stock-alerts') }}" class="nav-link {{ request()->routeIs('pharmacist.stock-alerts*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle fa-fw me-2"></i> Stock Alerts
                </a>
                
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link mt-auto">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </aside>

        <div class="content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse"></div>
                    
                    <ul class="navbar-nav ms-auto">
                        {{-- === UPDATED USER DROPDOWN START === --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline">{{ auth('pharmacist')->user()->name }}</span>
                                <div class="user-avatar">
                                    {{ strtoupper(substr(auth('pharmacist')->user()->name, 0, 1)) }}
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom" aria-labelledby="userDropdown">
                                <li>
                                    <div class="dropdown-header-custom">
                                        <h5>{{ auth('pharmacist')->user()->name }}</h5>
                                        <p>{{ auth('pharmacist')->user()->email }}</p>
                                    </div>
                                </li>
                                <!-- <li><a class="dropdown-item" href="#"><i class="fas fa-user-shield fa-fw"></i> Role</a></li> -->
                                <li><a class="dropdown-item" href="#"><i class="fas fa-lock fa-fw"></i> Change Password</a></li>
                                <li><hr class="dropdown-divider my-0"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-2').submit();">
                                        <i class="fas fa-sign-out-alt fa-fw"></i> Sign Out
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- === UPDATED USER DROPDOWN END === --}}
                    </ul>
                </div>
            </nav>

            <main class="main-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <form id="logout-form-2" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    @stack('scripts')
</body>
</html>
