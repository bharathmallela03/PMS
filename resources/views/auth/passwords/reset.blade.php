@extends('layouts.app')

@section('title', 'Reset Password')

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
                            <h2 class="card-title">Set a New Password</h2>
                            <p class="text-muted">Please enter your new password below.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password-confirm" name="password_confirmation" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
