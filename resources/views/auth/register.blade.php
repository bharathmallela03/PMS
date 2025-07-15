@extends('layouts.app')

@section('title', 'Customer Registration - Pharmacy Management System')

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Left side - Branding -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-success">
            <div class="text-center text-white">
                <i class="fas fa-user-plus fa-5x mb-4"></i>
                <h1 class="display-4 fw-bold">Join PharmaCare</h1>
                <p class="lead">Register now to order medicines online</p>
                <div class="row mt-5">
                    <div class="col-4">
                        <i class="fas fa-pills fa-2x mb-2"></i>
                        <p>Quality Medicines</p>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-shipping-fast fa-2x mb-2"></i>
                        <p>Fast Delivery</p>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-shield-alt fa-2x mb-2"></i>
                        <p>Secure Ordering</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Registration Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-100" style="max-width: 500px;">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
                            <h2 class="card-title">Create Account</h2>
                            <p class="text-muted">Join us to start ordering medicines online</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <div class="row">
                                <!-- Name -->
                                <div class="col-12 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name') }}" required autofocus>
                                    </div>
                                </div>

                                <!-- Contact Number -->
                                <div class="col-12 mb-3">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                               value="{{ old('contact_number') }}" required>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label">Email Address (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ old('email') }}">
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                    </div>
                                </div>

                                <!-- City & State -->
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="{{ old('city') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="state" 
                                           value="{{ old('state') }}" required>
                                </div>

                                <!-- Pincode & Country -->
                                <div class="col-md-6 mb-3">
                                    <label for="pincode" class="form-label">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" 
                                           value="{{ old('pincode') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" 
                                           value="{{ old('country', 'India') }}" required>
                                </div>

                                <!-- Password -->
                                <div class="col-12 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-12 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                </div>

                                <!-- Terms & Conditions -->
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" class="text-primary">Terms & Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Create Account
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center mt-4">
                            <p class="mb-0">Already have an account? 
                                <a href="{{ route('login', ['type' => 'customer']) }}" class="text-primary">Sign in here</a>
                            </p>
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