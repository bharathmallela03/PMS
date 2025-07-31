<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Customer Dashboard') - PharmaCare</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Custom styles for the layout */
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
            background-color: #0047FF; /* Changed to vibrant blue */
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
            border-bottom: 1px solid #003bde; /* Border to match hover state */
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
    </style>

    @stack('styles')
</head>
<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar d-none d-lg-block">
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
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link mt-auto">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="content-wrapper">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <!-- Mobile Menu Toggle -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse">
                        <!-- This can be used for breadcrumbs or page titles if needed -->
                    </div>
                    
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('customer.cart') }}">
                                <i class="fas fa-shopping-cart fs-5"></i>
                                <span class="cart-count badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="display: none;">0</span>
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fs-5 me-1"></i>
                                {{ auth('customer')->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}">
                                    <i class="fas fa-user fa-sm fa-fw me-2 text-muted"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.addresses') }}">
                                    <i class="fas fa-map-marker-alt fa-sm fa-fw me-2 text-muted"></i> Addresses
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-2').submit();">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-muted"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="main-content">
                 @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <!-- Other session messages here -->

                @yield('content')
            </main>
        </div>
    </div>

    <form id="logout-form-2" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('scripts')
</body>
</html>
