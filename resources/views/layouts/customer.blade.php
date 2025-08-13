<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>💊 @yield('title', 'Customer Dashboard') - PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Base layout styles */
        body {
            background-color: #f8f9fa;
        }
        .main-wrapper {
            display: flex;
        }
        
        /* Fixed Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #0047FF;
            color: white;
            z-index: 1030;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid #003bde;
        }
        .sidebar .nav-link {
            color: #e0e0e0;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            transition: background-color 0.2s ease-in-out;
        }
        .sidebar .nav-link:hover {
            background-color: #003bde;
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #0032ba;
            color: white;
            font-weight: bold;
        }
        .sidebar hr {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin: 1rem 1.5rem;
        }
        .sidebar-footer {
            padding: 1rem 1.5rem;
            font-size: 0.8rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: 260px;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
        }
        
        /* User Dropdown Styles */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e85a4f;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .dropdown-menu-custom {
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            padding: 0;
            overflow: hidden;
            min-width: 250px;
        }
        /* --- UPDATED: Reduced padding in dropdown header --- */
        .dropdown-header-custom {
            padding: 0.75rem 1.25rem; /* Reduced top/bottom padding */
            background-color: #0047FF;
            color: #fff;
        }
        .dropdown-header-custom h5 { margin-bottom: 0.25rem; font-weight: 600; font-size: 1rem; }
        .dropdown-header-custom p { font-size: 0.8rem; color: rgba(255, 255, 255, 0.85); margin-bottom: 0; }
        
        .dropdown-menu-custom .dropdown-item { padding: 0.75rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; }
        .dropdown-menu-custom .dropdown-item .fa-fw { color: #6b7280; }

        /* Mobile Offcanvas Sidebar */
        .offcanvas-header {
            background-color: #0047FF;
            color: white;
            border-bottom: 1px solid #003bde;
        }
        .offcanvas-body {
            background-color: #0047FF;
            padding: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .sidebar {
                display: none;
            }
            .content-wrapper {
                margin-left: 0;
            }
        }
        @media (max-width: 767.98px) {
            .main-content {
                padding: 1.5rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="main-wrapper">
        <aside class="sidebar">
            <div>
                <div class="sidebar-header">
                    <i class="fas fa-pills me-2"></i> PHARMACARE
                </div>
                <nav class="nav flex-column mt-3">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt fa-fw me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('customer.medicines') }}" class="nav-link {{ request()->routeIs('customer.medicines*') ? 'active' : '' }}">
                        <i class="fas fa-capsules fa-fw me-2"></i> Medicines
                    </a>
                    <a href="{{ route('customer.orders') }}" class="nav-link {{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice fa-fw me-2"></i> My Orders
                    </a>
                    <a href="{{ route('customer.profile') }}" class="nav-link {{ request()->routeIs('customer.profile*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle fa-fw me-2"></i> Profile
                    </a>
                </nav>
            </div>

            <div class="mt-auto">
                <hr>
                <nav class="nav flex-column">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                        <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                    </a>
                </nav>
                <hr>
                <div class="sidebar-footer">
                    &copy;  {{ date('Y') }} PMS
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </aside>

        <div class="content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse"></div>
                    
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('customer.cart') }}">
                                <i class="fas fa-shopping-cart fs-5"></i>
                                <span class="cart-count badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 0.6em; padding: 0.35em 0.5em;">0</span>
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline">{{ auth('customer')->user()->name }}</span>
                                <div class="user-avatar">
                                    {{ strtoupper(substr(auth('customer')->user()->name, 0, 1)) }}
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom" aria-labelledby="userDropdown">
                                <li>
                                    <div class="dropdown-header-custom">
                                        <h5>{{ auth('customer')->user()->name }}</h5>
                                        <p>{{ auth('customer')->user()->email }}</p>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                        <i class="fas fa-user-circle fa-fw"></i> Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-0"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.addresses') }}">
                                        <i class="fas fa-map-marker-alt fa-fw"></i> Addresses
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-0"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-2').submit();">
                                        <i class="fas fa-sign-out-alt fa-fw"></i> Sign Out
                                    </a>
                                </li>
                            </ul>
                        </li>
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
                @yield('content')
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel">
                <i class="fas fa-pills me-2"></i> PHARMACARE
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column">
                <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i> Dashboard
                </a>
                <a href="{{ route('customer.medicines') }}" class="nav-link {{ request()->routeIs('customer.medicines*') ? 'active' : '' }}">
                    <i class="fas fa-capsules fa-fw me-2"></i> Medicines
                </a>
                <a href="{{ route('customer.orders') }}" class="nav-link {{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice fa-fw me-2"></i> My Orders
                </a>
                <a href="{{ route('customer.profile') }}" class="nav-link {{ request()->routeIs('customer.profile*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle fa-fw me-2"></i> Profile
                </a>
                <hr style="border-top: 1px solid rgba(255, 255, 255, 0.2);">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-3').submit();" class="nav-link">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                </a>
            </nav>
        </div>
    </div>

    <form id="logout-form-2" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    <form id="logout-form-3" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('scripts')
</body>
</html>