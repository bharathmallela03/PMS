@extends('layouts.admin')

@section('title', 'Edit Pharmacist')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pharmacist</h1>
        <a href="{{ route('admin.pharmacists') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Pharmacist Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pharmacists.update', $pharmacist->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pharmacist->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $pharmacist->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number', $pharmacist->contact_number) }}" required>
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="license_number" class="form-label">License Number</label>
                        <input type="text" class="form-control @error('license_number') is-invalid @enderror" id="license_number" name="license_number" value="{{ old('license_number', $pharmacist->license_number) }}" required>
                        @error('license_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $pharmacist->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $pharmacist->city) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $pharmacist->state) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode', $pharmacist->pincode) }}">
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $pharmacist->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <hr class="my-4">

                <button type="submit" class="btn btn-primary">Update Pharmacist</button>
            </form>
        </div>
    </div>
</div>
@endsection
