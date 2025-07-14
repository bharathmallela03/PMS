@extends('layouts.app')

@section('title', 'Login - Pharmacy Management System')

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Left side - Branding -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-primary">
            <div class="text-center text-white">
                <i class="fas fa-pills fa-5x mb-4"></i>
                <h1 class="display-4 fw-bold">PharmaCare</h1>
                <p class="lead">Complete Pharmacy Management Solution</p>
                <div class="row mt-5">
                    <div class="col-4">
                        <i class="fas fa-user-md fa-2x mb-2"></i>
                        <p>Pharmacists</p>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-truck fa-2x mb-2"></i>
                        <p>Suppliers</p>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p>Customers</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-100" style="max-width: 400px;">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-pills fa-3x text-primary mb-3"></i>
                            <h2 class="card-title">Welcome Back</h2>
                            <p class="text-muted">Please sign in to your account</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info">
                                {{ session('info') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <!-- User Type Selection -->
                            <div class="mb-3">
                                <label for="user_type" class="form-label">Login As</label>
                                <select class="form-select" id="user_type" name="user_type" required>
                                    <option value="">Select User Type</option>
                                    <option value="admin" {{ $userType === 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="pharmacist" {{ $userType === 'pharmacist' ? 'selected' : '' }}>Pharmacist</option>
                                    <option value="supplier" {{ $userType === 'supplier' ? 'selected' : '' }}>Supplier</option>
                                    <option value="customer" {{ $userType === 'customer' ? 'selected' : '' }}>Customer</option>
                                </select>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" required autocomplete="email" autofocus>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </button>
                            </div>
                        </form>

                        <!-- Additional Links -->
                        <div class="text-center mt-4">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('register') }}" class="text-decoration-none">
                                        <small>New Customer? Register</small>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="#" class="text-decoration-none">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Demo Credentials -->
                        <div class="mt-4">
                            <h6 class="text-center text-muted">Demo Credentials:</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted">
                                        <strong>Admin:</strong><br>
                                        admin@pharmacy.com<br>
                                        password
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">
                                        <strong>Customer:</strong><br>
                                        customer@test.com<br>
                                        password
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    this.querySelector('i').classList.toggle('fa-eye');
    this.querySelector('i').classList.toggle('fa-eye-slash');
});
</script>
@endpush
@endsection