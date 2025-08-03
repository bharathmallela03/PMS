@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100">
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-primary">
            <div class="text-center text-white">
                <i class="fas fa-pills fa-5x mb-4"></i>
                <h1 class="display-4 fw-bold">PharmaCare</h1>
                <p class="lead">Complete Pharmacy Management Solution</p>
            </div>
        </div>

        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-100" style="max-width: 400px;">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="card-title">Reset Password</h2>
                            <p class="text-muted">Enter your email and we'll send you a link to reset your password.</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @error('email')
                            <div class="alert alert-danger" role="alert">
                                {{ $message }}
                            </div>
                        @enderror

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" required autocomplete="email" autofocus>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <small><i class="fas fa-arrow-left me-1"></i>Back to Login</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
